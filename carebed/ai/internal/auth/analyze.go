package analyzeai

import (
	"context"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"strconv"
	"strings"

	"google.golang.org/genai"
)


type SensorData struct {
	PatientName       string       `json:"patient_name,omitempty"`
	PatientAge        flexibleInt  `json:"patient_age,omitempty"`
	Age               flexibleInt  `json:"age,omitempty"`
	CurrentBPM        flexibleInt  `json:"current_bpm,omitempty"`
	HeartRate         flexibleInt  `json:"heart_rate,omitempty"`
	Temperature       flexibleFloat `json:"temperature,omitempty"`
	Wetness           string       `json:"wetness,omitempty"`
	PresenceDetection string       `json:"presence_detection,omitempty"`
	State             string       `json:"state,omitempty"`
	Duration          string       `json:"duration,omitempty"`
	DurationMinutes   flexibleInt  `json:"duration_minutes,omitempty"`
	Symptoms          stringList    `json:"symptoms,omitempty"`
}

type analysisInput struct {
	PatientName       string   `json:"patient_name,omitempty"`
	PatientAge        string   `json:"patient_age"`
	CurrentBPM        int      `json:"current_bpm"`
	TemperatureC      string   `json:"temperature_celsius,omitempty"`
	Wetness           string   `json:"wetness,omitempty"`
	PresenceDetection string   `json:"presence_detection,omitempty"`
	State             string   `json:"state"`
	Duration          string   `json:"duration"`
	Symptoms          []string `json:"symptoms"`
}

type flexibleInt int

func (n *flexibleInt) UnmarshalJSON(data []byte) error {
	raw := strings.TrimSpace(string(data))
	if raw == "" || raw == "null" {
		return nil
	}

	var number int
	if err := json.Unmarshal(data, &number); err == nil {
		*n = flexibleInt(number)
		return nil
	}

	var text string
	if err := json.Unmarshal(data, &text); err != nil {
		return err
	}

	text = strings.TrimSpace(text)
	if text == "" || strings.EqualFold(text, "N/A") || text == "--" {
		return nil
	}

	value, err := strconv.ParseFloat(text, 64)
	if err != nil {
		return nil
	}

	*n = flexibleInt(value)
	return nil
}

type flexibleFloat float64

func (n *flexibleFloat) UnmarshalJSON(data []byte) error {
	raw := strings.TrimSpace(string(data))
	if raw == "" || raw == "null" {
		return nil
	}

	var number float64
	if err := json.Unmarshal(data, &number); err == nil {
		*n = flexibleFloat(number)
		return nil
	}

	var text string
	if err := json.Unmarshal(data, &text); err != nil {
		return err
	}

	text = strings.TrimSpace(text)
	if text == "" || strings.EqualFold(text, "N/A") || text == "--" {
		return nil
	}

	value, err := strconv.ParseFloat(text, 64)
	if err != nil {
		return nil
	}

	*n = flexibleFloat(value)
	return nil
}

type stringList []string

func (s *stringList) UnmarshalJSON(data []byte) error {
	var values []string
	if err := json.Unmarshal(data, &values); err == nil {
		*s = cleanStrings(values)
		return nil
	}

	var text string
	if err := json.Unmarshal(data, &text); err != nil {
		return err
	}

	if strings.TrimSpace(text) == "" || strings.EqualFold(strings.TrimSpace(text), "none reported") {
		*s = nil
		return nil
	}

	*s = cleanStrings(strings.Split(text, ","))
	return nil
}


func (h *Handler) handlerAnalysis(w http.ResponseWriter, r *http.Request) {
	log.Println("AI Analysis request received")

	
	var data SensorData
	if err := json.NewDecoder(r.Body).Decode(&data); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	normalized := data.normalize()

	
	sensorJSON, _ := json.MarshalIndent(normalized, "", "  ")
	prompt := fmt.Sprintf("Analyze this live Carebed sensor reading. Use the provided context and inferred symptoms, but do not diagnose:\n%s", string(sensorJSON))

	
	result, err := h.Client.Models.GenerateContent(context.Background(), "gemini-2.5-flash", genai.Text(prompt), h.Config)
	if err != nil {
		http.Error(w, fmt.Sprintf("AI Error: %v", err), http.StatusInternalServerError)
		return
	}

	
	w.Header().Set("Content-Type", "application/json")
	fmt.Fprint(w, result.Text())
	fmt.Println(result.Text())
}

func (data SensorData) normalize() analysisInput {
	age := int(data.PatientAge)
	if age == 0 {
		age = int(data.Age)
	}

	bpm := int(data.CurrentBPM)
	if bpm == 0 {
		bpm = int(data.HeartRate)
	}

	state := strings.TrimSpace(data.State)
	if state == "" {
		state = inferState(data.PresenceDetection)
	}

	duration := strings.TrimSpace(data.Duration)
	if duration == "" && data.DurationMinutes > 0 {
		duration = fmt.Sprintf("%d minutes", data.DurationMinutes)
	}
	if duration == "" {
		duration = "latest sensor reading"
	}

	symptoms := []string(data.Symptoms)
	if len(symptoms) == 0 {
		symptoms = inferSymptoms(bpm, float64(data.Temperature), data.Wetness, data.PresenceDetection)
	}

	temperature := ""
	if data.Temperature != 0 {
		temperature = strconv.FormatFloat(float64(data.Temperature), 'f', 1, 64)
	}

	return analysisInput{
		PatientName:       strings.TrimSpace(data.PatientName),
		PatientAge:        ageText(age),
		CurrentBPM:        bpm,
		TemperatureC:      temperature,
		Wetness:           valueOrUnknown(data.Wetness),
		PresenceDetection: valueOrUnknown(data.PresenceDetection),
		State:             state,
		Duration:          duration,
		Symptoms:          symptoms,
	}
}

func inferState(presence string) string {
	switch strings.ToLower(strings.TrimSpace(presence)) {
	case "not detected", "no", "absent", "0":
		return "patient not detected on bed"
	default:
		return "resting / latest bed reading"
	}
}

func inferSymptoms(bpm int, temperature float64, wetness, presence string) []string {
	var symptoms []string

	if bpm > 0 && bpm < 60 {
		symptoms = append(symptoms, "low heart rate detected")
	}
	if bpm > 100 {
		symptoms = append(symptoms, "elevated heart rate detected")
	}
	if temperature >= 38 {
		symptoms = append(symptoms, "high temperature detected")
	}
	if temperature > 0 && temperature < 35.5 {
		symptoms = append(symptoms, "low temperature detected")
	}
	if containsAny(wetness, "wet", "moist", "damp", "detected", "yes", "1") {
		symptoms = append(symptoms, "wetness detected")
	}
	if containsAny(presence, "not detected", "absent", "no", "0") {
		symptoms = append(symptoms, "patient presence not detected")
	}

	if len(symptoms) == 0 {
		return []string{"none automatically detected from latest sensors"}
	}

	return symptoms
}

func cleanStrings(values []string) []string {
	cleaned := make([]string, 0, len(values))
	for _, value := range values {
		value = strings.TrimSpace(value)
		if value != "" {
			cleaned = append(cleaned, value)
		}
	}
	return cleaned
}

func ageText(age int) string {
	if age <= 0 {
		return "unknown"
	}
	return strconv.Itoa(age)
}

func valueOrUnknown(value string) string {
	value = strings.TrimSpace(value)
	if value == "" {
		return "unknown"
	}
	return value
}

func containsAny(value string, needles ...string) bool {
	value = strings.ToLower(strings.TrimSpace(value))
	for _, needle := range needles {
		if strings.Contains(value, needle) {
			return true
		}
	}
	return false
}
