
import { motion } from "framer-motion";
import { Trash } from "lucide-react";

interface PollOptionItemProps {
  option: string;
  index: number;
  handleOptionChange: (index: number, value: string) => void;
  handleRemoveOption: (index: number) => void;
  canRemove: boolean;
}

const PollOptionItem = ({
  option,
  index,
  handleOptionChange,
  handleRemoveOption,
  canRemove,
}: PollOptionItemProps) => {
  return (
    <motion.div
      initial={{ opacity: 0, height: 0 }}
      animate={{ opacity: 1, height: "auto" }}
      exit={{ opacity: 0, height: 0 }}
      transition={{ duration: 0.2 }}
      className="flex items-center gap-2"
    >
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
    </motion.div>
  );
};

export default PollOptionItem;
