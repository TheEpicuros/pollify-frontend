
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import AnimatedLogo from "./AnimatedLogo";

const Footer = () => {
  return (
    <footer className="border-t py-10 md:py-12 backdrop-blur-sm">
      <div className="container max-w-screen-xl mx-auto px-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
          <div className="space-y-4">
            <AnimatedLogo size="sm" />
            <p className="text-sm text-muted-foreground max-w-xs">
              Create, browse, and vote on polls with this elegant WordPress polling system.
            </p>
          </div>

          <div>
            <h4 className="font-medium text-base mb-4">Navigation</h4>
            <ul className="space-y-2">
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <Link to="/" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Home
                </Link>
              </motion.li>
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <Link to="/polls" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Browse Polls
                </Link>
              </motion.li>
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <Link to="/create" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Create Poll
                </Link>
              </motion.li>
            </ul>
          </div>

          <div>
            <h4 className="font-medium text-base mb-4">Resources</h4>
            <ul className="space-y-2">
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <Link to="#" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Documentation
                </Link>
              </motion.li>
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <Link to="#" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  API Reference
                </Link>
              </motion.li>
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <Link to="#" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Privacy Policy
                </Link>
              </motion.li>
            </ul>
          </div>

          <div>
            <h4 className="font-medium text-base mb-4">Contact</h4>
            <ul className="space-y-2">
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <a href="mailto:support@pollify.com" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  support@pollify.com
                </a>
              </motion.li>
              <motion.li whileHover={{ x: 2 }} transition={{ type: "spring", stiffness: 400, damping: 10 }}>
                <a href="#" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Contact Form
                </a>
              </motion.li>
            </ul>
          </div>
        </div>
        
        <div className="mt-10 pt-6 border-t text-center">
          <p className="text-sm text-muted-foreground">
            © {new Date().getFullYear()} Pollify. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
