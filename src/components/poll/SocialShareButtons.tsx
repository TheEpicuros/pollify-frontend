
import { Share2, Facebook, Twitter, Linkedin, Link, Copy } from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";
import { 
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";

interface SocialShareButtonsProps {
  pollTitle: string;
  pollUrl: string;
  onShare?: (platform: string) => void;
  size?: "sm" | "md" | "lg";
}

const SocialShareButtons = ({ 
  pollTitle, 
  pollUrl, 
  onShare,
  size = "md" 
}: SocialShareButtonsProps) => {
  const [copied, setCopied] = useState(false);

  const encodedTitle = encodeURIComponent(pollTitle);
  const encodedUrl = encodeURIComponent(pollUrl);

  const shareLinks = {
    twitter: `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
    linkedin: `https://www.linkedin.com/shareArticle?mini=true&url=${encodedUrl}&title=${encodedTitle}`
  };

  const handleShare = (platform: string) => {
    window.open(shareLinks[platform as keyof typeof shareLinks], '_blank');
    toast.success(`Shared on ${platform}`);
    if (onShare) onShare(platform);
  };

  const copyToClipboard = () => {
    navigator.clipboard.writeText(pollUrl);
    setCopied(true);
    toast.success("Link copied to clipboard");
    setTimeout(() => setCopied(false), 2000);
    if (onShare) onShare("copy");
  };

  // Icon sizes based on the size prop
  const iconSize = size === "sm" ? 14 : size === "lg" ? 20 : 16;
  const buttonSize = size === "sm" ? "sm" : size === "lg" ? "lg" : "default";

  return (
    <div className="social-share-buttons">
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="outline" size={buttonSize} className="flex items-center gap-2">
            <Share2 size={iconSize} /> 
            <span>Share</span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent className="w-56">
          <DropdownMenuGroup>
            <DropdownMenuItem onClick={() => handleShare("twitter")}>
              <Twitter className="mr-2 h-4 w-4" />
              <span>Twitter</span>
            </DropdownMenuItem>
            <DropdownMenuItem onClick={() => handleShare("facebook")}>
              <Facebook className="mr-2 h-4 w-4" />
              <span>Facebook</span>
            </DropdownMenuItem>
            <DropdownMenuItem onClick={() => handleShare("linkedin")}>
              <Linkedin className="mr-2 h-4 w-4" />
              <span>LinkedIn</span>
            </DropdownMenuItem>
            <DropdownMenuItem onClick={copyToClipboard}>
              {copied ? (
                <>
                  <Copy className="mr-2 h-4 w-4" />
                  <span>Copied!</span>
                </>
              ) : (
                <>
                  <Link className="mr-2 h-4 w-4" />
                  <span>Copy link</span>
                </>
              )}
            </DropdownMenuItem>
          </DropdownMenuGroup>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
};

export default SocialShareButtons;
