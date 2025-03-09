
import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Search, SlidersHorizontal, X } from "lucide-react";
import { getAllPolls } from "@/lib/data";
import { Poll } from "@/lib/types";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import PollCard from "@/components/PollCard";

const PollBrowse = () => {
  const [polls, setPolls] = useState<Poll[]>([]);
  const [searchTerm, setSearchTerm] = useState("");
  const [isLoading, setIsLoading] = useState(true);
  const [showFilters, setShowFilters] = useState(false);
  const [sortOption, setSortOption] = useState("newest");
  const [statusFilter, setStatusFilter] = useState("all");

  useEffect(() => {
    // Simulate API fetch
    setTimeout(() => {
      const fetchedPolls = getAllPolls();
      setPolls(fetchedPolls);
      setIsLoading(false);
    }, 800);
  }, []);

  const filteredPolls = polls.filter((poll) => {
    const matchesSearch = poll.title
      .toLowerCase()
      .includes(searchTerm.toLowerCase());
    const matchesStatus =
      statusFilter === "all" ||
      (statusFilter === "active" && poll.status === "active") ||
      (statusFilter === "closed" && poll.status === "closed");
    return matchesSearch && matchesStatus;
  });

  const sortedPolls = [...filteredPolls].sort((a, b) => {
    switch (sortOption) {
      case "newest":
        return new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime();
      case "oldest":
        return new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime();
      case "most-votes":
        return b.totalVotes - a.totalVotes;
      case "least-votes":
        return a.totalVotes - b.totalVotes;
      default:
        return 0;
    }
  });

  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />

      <main className="flex-grow pt-20">
        <div className="page-container">
          <div className="max-w-4xl mx-auto">
            <div className="text-center mb-8 md:mb-12">
              <motion.h1
                initial={{ opacity: 0, y: -10 }}
                animate={{ opacity: 1, y: 0 }}
                className="heading-2 mb-4"
              >
                Browse Polls
              </motion.h1>
              <motion.p
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: 0.1 }}
                className="text-muted-foreground"
              >
                Discover and vote on polls created by the community
              </motion.p>
            </div>

            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
              className="mb-8"
            >
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search className="h-5 w-5 text-muted-foreground" />
                </div>
                <input
                  type="text"
                  className="block w-full pl-10 pr-12 py-3 border rounded-xl bg-background/80 backdrop-blur-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
                  placeholder="Search polls..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
                {searchTerm && (
                  <button
                    className="absolute inset-y-0 right-12 flex items-center pr-3"
                    onClick={() => setSearchTerm("")}
                  >
                    <X className="h-4 w-4 text-muted-foreground hover:text-foreground" />
                  </button>
                )}
                <button
                  className="absolute inset-y-0 right-0 pr-3 flex items-center"
                  onClick={() => setShowFilters(!showFilters)}
                >
                  <SlidersHorizontal
                    className={`h-5 w-5 transition-colors ${
                      showFilters
                        ? "text-primary"
                        : "text-muted-foreground hover:text-foreground"
                    }`}
                  />
                </button>
              </div>

              <AnimatePresence>
                {showFilters && (
                  <motion.div
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ opacity: 1, height: "auto" }}
                    exit={{ opacity: 0, height: 0 }}
                    transition={{ duration: 0.2 }}
                    className="mt-4 p-4 bg-background/80 backdrop-blur-sm border rounded-xl"
                  >
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <label className="block text-sm font-medium mb-1">
                          Sort by
                        </label>
                        <select
                          value={sortOption}
                          onChange={(e) => setSortOption(e.target.value)}
                          className="w-full p-2 border rounded-lg bg-background"
                        >
                          <option value="newest">Newest first</option>
                          <option value="oldest">Oldest first</option>
                          <option value="most-votes">Most votes</option>
                          <option value="least-votes">Least votes</option>
                        </select>
                      </div>
                      <div>
                        <label className="block text-sm font-medium mb-1">
                          Status
                        </label>
                        <select
                          value={statusFilter}
                          onChange={(e) => setStatusFilter(e.target.value)}
                          className="w-full p-2 border rounded-lg bg-background"
                        >
                          <option value="all">All polls</option>
                          <option value="active">Active only</option>
                          <option value="closed">Closed only</option>
                        </select>
                      </div>
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </motion.div>

            {isLoading ? (
              <div className="space-y-4">
                {[1, 2, 3, 4].map((i) => (
                  <div
                    key={i}
                    className="rounded-xl h-48 bg-secondary animate-pulse"
                  />
                ))}
              </div>
            ) : (
              <>
                {sortedPolls.length > 0 ? (
                  <div className="space-y-4">
                    {sortedPolls.map((poll, index) => (
                      <PollCard key={poll.id} poll={poll} index={index} />
                    ))}
                  </div>
                ) : (
                  <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    className="text-center py-16"
                  >
                    <p className="text-muted-foreground mb-4">
                      No polls match your search criteria
                    </p>
                    <button
                      className="text-primary hover:underline"
                      onClick={() => {
                        setSearchTerm("");
                        setStatusFilter("all");
                        setSortOption("newest");
                      }}
                    >
                      Reset filters
                    </button>
                  </motion.div>
                )}
              </>
            )}
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
};

export default PollBrowse;
