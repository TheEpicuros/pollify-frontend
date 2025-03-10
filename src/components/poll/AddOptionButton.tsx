
import { Plus } from "lucide-react";

interface AddOptionButtonProps {
  handleAddOption: () => void;
  disabled: boolean;
}

const AddOptionButton: React.FC<AddOptionButtonProps> = ({
  handleAddOption,
  disabled,
}) => {
  return (
    <button
      type="button"
      onClick={handleAddOption}
      disabled={disabled}
      className="w-full py-2 px-4 border border-dashed rounded-lg text-sm font-medium flex items-center justify-center gap-2 text-muted-foreground hover:text-foreground hover:border-foreground/30 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
    >
      <Plus size={16} />
      Add Option
    </button>
  );
};

export default AddOptionButton;
