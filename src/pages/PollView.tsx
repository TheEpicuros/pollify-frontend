
import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { motion, AnimatePresence } from "framer-motion";
import { format } from "date-fns";
import { getPoll, voteOnPoll } from "@/lib/data";
import { Poll as PollType } from "@/lib/types";
import { ChevronLeft, Clock, User, Share2 } from "lucide-react";
import { toast } from "sonner";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import PollResults from "@/components/PollResults";

const PollView = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [poll, setPoll] = useState<PollType | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedOption, setSelectedOption] = useState<string | null>(null);
  const [hasVoted, setHasVoted] = useState<string | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showResults, setShowResults] = useState(false);

  useEffect(() => {
    if (!id) {
      navigate("/polls");
      return;
    }

    // Simulate API fetch
    setTimeout(() => {
      const fetchedPoll = getPoll(id);
      if (fetchedPoll) {
        setPoll(fetchedPoll);
      } else {
        toast.error("Poll not found");
        navigate("/polls");
      }
      setIsLoading(false);
    }, 800);

    // Check if user has already voted
    const votedPolls = localStorage.getItem("votedPolls");
    if (votedPolls) {
      const parsed = JSON.parse(votedPolls);
      if (parsed[id]) {
        setHasVoted(parsed[id]);
        setShowResults(true);
      }
    }
  }, [id, navigate]);

  const handleVote = () => {
    if (!poll || !selectedOption) return;
    
    setIsSubmitting(true);
    
    // Simulate API call
    setTimeout(() => {
      const success = voteOnPoll(poll.id, selectedOption);
      
      if (success) {
        // Save vote to localStorage
        const votedPolls = JSON.parse(localStorage.getItem("votedPolls") || "{}");
        votedPolls[poll.id] = selectedOption;
        localStorage.setItem("votedPolls", JSON.stringify(votedPolls));
        
        setHasVoted(selectedOption);
        setShowResults(true);
        toast.success("Your vote has been counted!");
        
        // Update poll data
        const updatedPoll = getPoll(poll.id);
        if (updatedPoll) {
          setPoll(updatedPoll);
        }
      } else {
        toast.error("Failed to submit your vote. Please try again.");
      }
      
      setIsSubmitting(false);
    }, 800);
  };

  const handleShare = async () => {
    const shareUrl = window.location.href;
    
    if (navigator.share) {
      try {
        await navigator.share({
          title: poll?.title,
          text: poll?.description,
          url: shareUrl,
        });
      } catch (error) {
        console.error("Error sharing:", error);
        copyToClipboard(shareUrl);
      }
    } else {
      copyToClipboard(shareUrl);
    }
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text)
      .then(() => toast.success("Poll link copied to clipboard!"))
      .catch(err => console.error("Failed to copy:", err));
  };

  if (isLoading) {
    return (
      <div className="min-h-screen flex flex-col">
        <Navbar />
        <main className="flex-grow pt-20 flex items-center justify-center">
          <div className="animate-pulse flex flex-col items-center">
            <div className="h-8 w-64 bg-secondary rounded-full mb-4" />
            <div className="h-4 w-48 bg-secondary rounded-full" />
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  if (!poll) {
    return (
      <div className="min-h-screen flex flex-col">
        <Navbar />
        <main className="flex-grow pt-20">
          <div className="container text-center py-16">
            <h1 className="heading-2 mb-4">Poll Not Found</h1>
            <p className="text-muted-foreground mb-8">
              The poll you're looking for doesn't exist or has been removed.
            </p>
            <button
              onClick={() => navigate("/polls")}
              className="inline-flex items-center text-primary hover:underline"
            >
              <ChevronLeft size={16} className="mr-1" />
              Back to all polls
            </button>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />
      
      <main className="flex-grow pt-20">
        <div className="page-container">
          <div className="max-w-3xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="mb-8"
            >
              <button
                onClick={() => navigate("/polls")}
                className="inline-flex items-center text-muted-foreground hover:text-foreground transition-colors"
              >
                <ChevronLeft size={16} className="mr-1" />
                Back to all polls
              </button>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="glass-card p-6 sm:p-8 rounded-xl mb-6"
            >
              <div className="mb-6">
                <div className="flex items-center justify-between mb-4">
                  <span className={`inline-block text-xs font-medium px-2.5 py-0.5 rounded-full ${
                    poll.status === "active"
                      ? "bg-primary/10 text-primary"
                      : "bg-muted-foreground/10 text-muted-foreground"
                  }`}>
                    {poll.status === "active" ? "Active Poll" : "Closed Poll"}
                  </span>
                  
                  <button
                    onClick={handleShare}
                    className="p-2 hover:bg-secondary rounded-full transition-colors"
                    aria-label="Share poll"
                  >
                    <Share2 size={18} className="text-muted-foreground" />
                  </button>
                </div>
                
                <h1 className="text-2xl font-bold text-balance">{poll.title}</h1>
                
                {poll.description && (
                  <p className="mt-2 text-muted-foreground">{poll.description}</p>
                )}
                
                <div className="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted-foreground">
                  <div className="flex items-center">
                    <Clock size={14} className="mr-1" />
                    {format(new Date(poll.createdAt), "MMM d, yyyy")}
                  </div>
                  <div className="flex items-center">
                    <User size={14} className="mr-1" />
                    {poll.createdBy}
                  </div>
                </div>
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                  <h3 className="text-lg font-medium mb-4 text-center sm:text-left">
                    {hasVoted ? "You voted" : "Cast your vote"}
                  </h3>
                  
                  <div className="space-y-3">
                    {poll.options.map((option) => (
                      <button
                        key={option.id}
                        disabled={hasVoted !== null}
                        onClick={() => setSelectedOption(option.id)}
                        className={`w-full text-left px-4 py-3 rounded-lg border transition-all ${
                          hasVoted === option.id
                            ? "bg-primary/10 border-primary text-primary"
                            : selectedOption === option.id
                            ? "bg-primary/5 border-primary"
                            : "bg-background hover:bg-secondary/50"
                        }`}
                      >
                        <div className="flex items-center gap-3">
                          <span
                            className={`w-4 h-4 rounded-full border flex-shrink-0 ${
                              hasVoted === option.id || selectedOption === option.id
                                ? "border-primary"
                                : "border-muted-foreground"
                            }`}
                          >
                            {(hasVoted === option.id || selectedOption === option.id) && (
                              <span className="block w-2 h-2 rounded-full bg-primary m-auto" />
                            )}
                          </span>
                          <span>{option.text}</span>
                        </div>
                      </button>
                    ))}
                  </div>
                  
                  {!hasVoted && (
                    <div className="mt-6 flex flex-col sm:flex-row items-center gap-3">
                      <button
                        onClick={handleVote}
                        disabled={!selectedOption || isSubmitting}
                        className="w-full sm:w-auto bg-primary text-primary-foreground font-medium py-2 px-4 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-primary/90 transition-colors flex items-center justify-center"
                      >
                        {isSubmitting ? (
                          <>
                            <svg
                              className="animate-spin -ml-1 mr-2 h-4 w-4 text-primary-foreground"
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                            >
                              <circle
                                className="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                strokeWidth="4"
                              ></circle>
                              <path
                                className="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                              ></path>
                            </svg>
                            Submitting...
                          </>
                        ) : (
                          "Submit Vote"
                        )}
                      </button>
                      
                      {!showResults && (
                        <button
                          onClick={() => setShowResults(true)}
                          className="w-full sm:w-auto py-2 px-4 border rounded-lg text-sm font-medium hover:bg-secondary transition-colors"
                        >
                          View Results
                        </button>
                      )}
                    </div>
                  )}
                </div>
                
                <AnimatePresence mode="wait">
                  {showResults && (
                    <motion.div
                      initial={{ opacity: 0, x: 20 }}
                      animate={{ opacity: 1, x: 0 }}
                      exit={{ opacity: 0, x: 20 }}
                      transition={{ duration: 0.3 }}
                    >
                      <PollResults poll={poll} voted={hasVoted} />
                      
                      {!hasVoted && (
                        <div className="mt-6 text-center">
                          <button
                            onClick={() => setShowResults(false)}
                            className="text-sm text-muted-foreground hover:text-foreground transition-colors"
                          >
                            Hide results to vote
                          </button>
                        </div>
                      )}
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            </motion.div>
          </div>
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default PollView;
