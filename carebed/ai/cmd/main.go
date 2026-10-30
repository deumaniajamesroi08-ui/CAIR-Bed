package main

import (
	analyzeai "carebed/internal/auth"
	"context"
	"fmt"
	"log"
	"net/http"
	"os"

	"github.com/joho/godotenv"
	"google.golang.org/genai"
)


type SensorData struct {
	PatientAge      int      `json:"patient_age"`
	CurrentBPM      int      `json:"current_bpm"`
	State           string   `json:"state"`
	DurationMinutes int      `json:"duration_minutes"`
	Symptoms        []string `json:"symptoms"`
}

var client *genai.Client
var config *genai.GenerateContentConfig

func main() {
	
	err := godotenv.Load(".env", "../.env")
	if err != nil {
		log.Println("No .env file found, looking for system environment variables")
	}

	
	apiKey := os.Getenv("AI")
	if apiKey == "" {
		apiKey = os.Getenv("API_KEY")
	}
	if apiKey == "" {
		apiKey = os.Getenv("GEMINI_KEY")
	}
	if apiKey == "" {
		log.Fatal("AI, API_KEY, or GEMINI_KEY is not set")
	}

	
	ctx := context.Background()
	client, err = genai.NewClient(ctx, &genai.ClientConfig{APIKey: apiKey})
	if err != nil {
		log.Fatalf("Failed to create client: %v", err)
	}

	
	responseSchema := &genai.Schema{
		Type: genai.TypeObject,
		Properties: map[string]*genai.Schema{
			"analysis":                {Type: genai.TypeString},
			"severity_level":          {Type: genai.TypeString},
			"potential_benign_causes": {Type: genai.TypeArray, Items: &genai.Schema{Type: genai.TypeString}},
			"potential_illnesses":     {Type: genai.TypeArray, Items: &genai.Schema{Type: genai.TypeString}},
			"action_plan":             {Type: genai.TypeString},
			"medical_disclaimer":      {Type: genai.TypeString},
		},
	}

	
	config = &genai.GenerateContentConfig{
		SystemInstruction: &genai.Content{
			Parts: []*genai.Part{{Text: "You are Carebed AI, a backend analysis engine for a health app. " +
				"Analyze heart rate data. CRITICAL: You are not a doctor. " +
				"Never make a definitive diagnosis. Include a medical disclaimer."}},
		},
		ResponseMIMEType: "application/json",
		ResponseSchema:   responseSchema,
		Temperature:      genai.Ptr[float32](0.1),
	}

	
	analyze := analyzeai.NewHandler(client, config)

	
	mux := http.NewServeMux()

	
	mux.HandleFunc("GET /bedcare", serveHTML)
	mux.HandleFunc("POST /api/ai/v1/analyze", analyze.HandleAnalysis)

	
	fmt.Println("Carebed AI Server running on http://localhost:3000")
	log.Fatal(http.ListenAndServe(":3000", mux))
}


func serveHTML(w http.ResponseWriter, r *http.Request) {
	http.ServeFile(w, r, "index.html")
}
