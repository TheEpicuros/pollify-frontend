
import React from "react";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";

interface BinaryPollFormProps {
  options: string[];
  handleOptionChange: (index: number, value: string) => void;
}

const BinaryPollForm: React.FC<BinaryPollFormProps> = ({
  options,
  handleOptionChange,
}) => {
  return (
    <div className="space-y-4 mt-4">
      <div className="flex flex-col space-y-3">
        <div className="grid grid-cols-2 gap-4">
          <div className="space-y-2">
            <Label htmlFor="yes-option">Yes/Positive Option</Label>
            <Input
              id="yes-option"
              value={options[0]}
              onChange={(e) => handleOptionChange(0, e.target.value)}
              placeholder="Yes"
              className="border-green-500 focus:ring-green-500"
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="no-option">No/Negative Option</Label>
            <Input
              id="no-option"
              value={options[1]}
              onChange={(e) => handleOptionChange(1, e.target.value)}
              placeholder="No"
              className="border-red-500 focus:ring-red-500"
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default BinaryPollForm;
