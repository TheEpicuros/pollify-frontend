
import { motion } from "framer-motion";
import { ChartBar } from "lucide-react";

interface AnimatedLogoProps {
  size?: "sm" | "md" | "lg";
  withText?: boolean;
}

const AnimatedLogo = ({ size = "md", withText = true }: AnimatedLogoProps) => {
  const sizes = {
    sm: {
      icon: 24,
      text: "text-lg font-medium",
    },
    md: {
      icon: 32,
      text: "text-xl font-medium",
    },
    lg: {
      icon: 48,
      text: "text-3xl font-bold",
    },
  };

  return (
    <div className="flex items-center gap-2">
      <motion.div
        initial={{ scale: 0.8, opacity: 0 }}
        animate={{ scale: 1, opacity: 1 }}
        transition={{
          type: "spring",
          stiffness: 260,
          damping: 20,
          delay: 0.1,
        }}
        className="relative"
      >
        <motion.div
          animate={{
            scale: [1, 1.05, 1],
          }}
          transition={{
            duration: 3,
            repeat: Infinity,
            repeatType: "reverse",
            ease: "easeInOut",
          }}
          className="absolute inset-0 bg-primary/20 blur-xl rounded-full"
          style={{ width: sizes[size].icon, height: sizes[size].icon }}
        />
        <ChartBar
          size={sizes[size].icon}
          className="text-primary relative z-10"
        />
      </motion.div>
      {withText && (
        <motion.span
          initial={{ opacity: 0, x: -5 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ delay: 0.2, duration: 0.3 }}
          className={`${sizes[size].text} tracking-tight`}
        >
          Pollify
        </motion.span>
      )}
    </div>
  );
};

export default AnimatedLogo;
