import argparse
from summarizer import summarize_text

def main():
    parser = argparse.ArgumentParser(description="Summarize text from the command line.")
    parser.add_argument("text", type=str, help="The text to summarize")
    args = parser.parse_args()

    result = summarize_text(args.text)
    print("\nSummary:\n", result)

if __name__ == "__main__":
    main()