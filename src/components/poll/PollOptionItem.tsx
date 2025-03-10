
import { motion } from "framer-motion";
import { Trash, ImagePlus } from "lucide-react";
import { useState } from "react";

interface PollOptionItemProps {
  option: string;
  index: number;
  handleOptionChange: (index: number, value: string) => void;
  handleRemoveOption: (index: number) => void;
  canRemove: boolean;
  imageUrl?: string;
  onImageUpload?: (index: number, file: File) => void;
}

const PollOptionItem = ({
  option,
  index,
  handleOptionChange,
  handleRemoveOption,
  canRemove,
  imageUrl,
  onImageUpload,
}: PollOptionItemProps) => {
  const [isHovered, setIsHovered] = useState(false);
  const fileInputRef = useState<HTMLInputElement | null>(null)[1];

  const handleImageClick = () => {
    if (fileInputRef) {
      fileInputRef.click();
    }
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = e.target.files;
    if (files && files.length > 0 && onImageUpload) {
      onImageUpload(index, files[0]);
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0, height: 0 }}
      animate={{ opacity: 1, height: "auto" }}
      exit={{ opacity: 0, height: 0 }}
      transition={{ duration: 0.2 }}
      className="flex flex-col gap-2"
    >
      <div className="flex items-center gap-2">
        <input
          type="text"
          className="flex-1 px-4 py-2 rounded-lg border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
          placeholder={`Option ${index + 1}`}
          value={option}
          onChange={(e) => handleOptionChange(index, e.target.value)}
          required
        />
        <button
          type="button"
          onClick={() => handleRemoveOption(index)}
          className="p-2 text-muted-foreground hover:text-destructive rounded-lg hover:bg-destructive/10 transition"
          aria-label="Remove option"
          disabled={!canRemove}
        >
          <Trash size={18} />
        </button>
      </div>

      {onImageUpload && (
        <div 
          className="relative"
          onMouseEnter={() => setIsHovered(true)}
          onMouseLeave={() => setIsHovered(false)}
        >
          {imageUrl ? (
            <div className="relative rounded-lg overflow-hidden h-24 bg-muted">
              <img 
                src={imageUrl} 
                alt={`Option ${index + 1}`} 
                className="w-full h-full object-cover"
              />
              {isHovered && (
                <div className="absolute inset-0 bg-black/50 flex items-center justify-center">
                  <button
                    type="button"
                    onClick={handleImageClick}
                    className="p-2 bg-background rounded-full"
                  >
                    <ImagePlus size={18} />
                  </button>
                </div>
              )}
            </div>
          ) : (
            <button
              type="button"
              onClick={handleImageClick}
              className="w-full h-24 border border-dashed rounded-lg flex items-center justify-center text-muted-foreground hover:text-foreground hover:border-foreground/30 transition-colors"
            >
              <ImagePlus size={18} className="mr-2" />
              <span className="text-sm">Add Image</span>
            </button>
          )}
          <input
            type="file"
            ref={(ref) => fileInputRef = ref}
            className="hidden"
            accept="image/*"
            onChange={handleFileChange}
          />
        </div>
      )}
    </motion.div>
  );
};

export default PollOptionItem;
