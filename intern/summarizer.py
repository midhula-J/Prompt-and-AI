from transformers import pipeline

# Load model once
summarizer = pipeline("summarization", model="t5-small", tokenizer="t5-small")

def summarize_text(text):
    if not text.strip():
        return "No input text provided."
    summary = summarizer(text, max_length=100, min_length=30, do_sample=False)
    return summary[0]['summary_text']
