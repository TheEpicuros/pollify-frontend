
interface PollOptionsHeaderProps {
  optionsCount: number;
}

const PollOptionsHeader: React.FC<PollOptionsHeaderProps> = ({
  optionsCount,
}) => {
  return (
    <div className="flex items-center justify-between">
      <label className="block text-sm font-medium">
        Poll Options <span className="text-destructive">*</span>
      </label>
      <span className="text-xs text-muted-foreground">
        {optionsCount}/10 options
      </span>
    </div>
  );
};

export default PollOptionsHeader;
