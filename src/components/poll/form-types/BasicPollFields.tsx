
import React from "react";

interface BasicPollFieldsProps {
  title: string;
  description: string;
  onTitleChange: (value: string) => void;
  onDescriptionChange: (value: string) => void;
}

const BasicPollFields: React.FC<BasicPollFieldsProps> = ({
  title,
  description,
  onTitleChange,
  onDescriptionChange,
}) => {
  return (
    <>
      <div className="space-y-2">
        <label htmlFor="title" className="block text-sm font-medium">
          Poll Title <span className="text-destructive">*</span>
        </label>
        <input
          id="title"
          type="text"
          className="w-full px-4 py-2 rounded-lg border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
          placeholder="Ask a question..."
          value={title}
          onChange={(e) => onTitleChange(e.target.value)}
          required
        />
      </div>

      <div className="space-y-2">
        <label htmlFor="description" className="block text-sm font-medium">
          Description <span className="text-muted-foreground">(optional)</span>
        </label>
        <textarea
          id="description"
          rows={3}
          className="w-full px-4 py-2 rounded-lg border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition resize-none"
          placeholder="Add more context to your question..."
          value={description}
          onChange={(e) => onDescriptionChange(e.target.value)}
        />
      </div>
    </>
  );
};

export default BasicPollFields;
