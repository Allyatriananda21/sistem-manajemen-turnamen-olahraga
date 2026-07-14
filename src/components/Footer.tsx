import React from 'react';
import { Link } from 'react-router-dom';
import { Mail, Linkedin, Twitter, Instagram, Trophy } from 'lucide-react';

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer id="main-footer" className="relative w-full pt-12 pb-6 bg-transparent overflow-hidden">
      {/* Footer Container */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {/* Floating Glassmorphic Card */}
        <div className="glass-panel text-white rounded-[2.5rem] p-8 sm:p-12 lg:p-14 shadow-[0_24px_50px_-12px_rgba(0,0,0,0.5)] border border-white/10">
          <div className="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 pb-10 border-b border-white/10">
            
            {/* Left Column - Brand Info (Col span 8 on md) */}
            <div className="md:col-span-8 space-y-6">
              {/* Logo & Title */}
              <Link to="/" className="flex items-center gap-3 group w-fit">
                <div className="w-10 h-10 rounded-xl bg-[#E4FD97]/10 flex items-center justify-center border border-[#E4FD97]/20 group-hover:scale-105 transition-transform duration-300">
                  <Trophy className="w-5 h-5 text-[#E4FD97]" />
                </div>
                <span className="font-display font-extrabold text-2xl tracking-tight text-white group-hover:text-[#E4FD97] transition-colors uppercase">
                  TrophyHub
                </span>
              </Link>

              {/* Description */}
              <p className="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl">
                TrophyHub is a trusted league partner, providing expert tournament management and automated solutions to help teams and leagues navigate complex sporting challenges with confidence.
              </p>

              {/* Social Media Icons */}
              <div className="flex items-center gap-4 pt-2">
                <a 
                  href="#" 
                  className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:text-[#E4FD97] hover:bg-[#E4FD97]/10 hover:border-[#E4FD97]/20 hover:scale-110 transition-all duration-200"
                  aria-label="Mail"
                >
                  <Mail className="w-5 h-5" />
                </a>
                <a 
                  href="#" 
                  className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:text-[#E4FD97] hover:bg-[#E4FD97]/10 hover:border-[#E4FD97]/20 hover:scale-110 transition-all duration-200"
                  aria-label="LinkedIn"
                >
                  <Linkedin className="w-5 h-5" />
                </a>
                <a 
                  href="#" 
                  className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:text-[#E4FD97] hover:bg-[#E4FD97]/10 hover:border-[#E4FD97]/20 hover:scale-110 transition-all duration-200"
                  aria-label="Twitter"
                >
                  <Twitter className="w-5 h-5" />
                </a>
                <a 
                  href="#" 
                  className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:text-[#E4FD97] hover:bg-[#E4FD97]/10 hover:border-[#E4FD97]/20 hover:scale-110 transition-all duration-200"
                  aria-label="Instagram"
                >
                  <Instagram className="w-5 h-5" />
                </a>
              </div>
            </div>

            {/* Right Column - Quick Links (Col span 4 on md) */}
            <div className="md:col-span-4 space-y-4 md:pl-8">
              <h4 className="font-sans font-bold text-white text-lg tracking-tight">
                Quick Link
              </h4>
              <ul className="space-y-2.5 text-sm sm:text-base">
                <li>
                  <Link 
                    to="/" 
                    onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
                    className="text-slate-300 hover:text-[#E4FD97] font-semibold transition-colors"
                  >
                    Home
                  </Link>
                </li>
                <li>
                  <Link 
                    to="/" 
                    onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
                    className="text-slate-300 hover:text-[#E4FD97] font-semibold transition-colors"
                  >
                    About Us
                  </Link>
                </li>
                <li>
                  <Link to="/register" className="text-slate-300 hover:text-[#E4FD97] font-semibold transition-colors">
                    Services
                  </Link>
                </li>
                <li>
                  <Link to="/teams" className="text-slate-300 hover:text-[#E4FD97] font-semibold transition-colors">
                    Team
                  </Link>
                </li>
                <li>
                  <Link to="/matches" className="text-slate-300 hover:text-[#E4FD97] font-semibold transition-colors">
                    Project
                  </Link>
                </li>
                <li>
                  <Link to="/standings" className="text-slate-300 hover:text-[#E4FD97] font-semibold transition-colors">
                    Blog
                  </Link>
                </li>
              </ul>
            </div>
          </div>

          {/* Bottom Copyright and Meta Links */}
          <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs sm:text-sm text-slate-400 font-sans">
            <p>&copy; All rights reserved</p>
            <div className="flex flex-wrap gap-x-8 gap-y-2 justify-center">
              <a href="#" className="hover:text-[#E4FD97] transition-colors underline decoration-slate-700 hover:decoration-[#E4FD97]">
                Privacy Policy
              </a>
              <a href="#" className="hover:text-[#E4FD97] transition-colors underline decoration-slate-700 hover:decoration-[#E4FD97]">
                Terms & Conditions
              </a>
              <a href="#" className="hover:text-[#E4FD97] transition-colors underline decoration-slate-700 hover:decoration-[#E4FD97]">
                Cookies Settings
              </a>
            </div>
          </div>
        </div>
      </div>

      {/* Giant Decorative Faded Background Text "TrophyHub" at the very bottom */}
      <div className="absolute bottom-[-2.5rem] left-0 right-0 text-center pointer-events-none select-none z-0 overflow-hidden h-[18vw] max-h-[160px] min-h-[80px]">
        <h2 className="font-display font-extrabold text-[15vw] leading-none tracking-widest text-white/[0.04] uppercase whitespace-nowrap">
          TrophyHub
        </h2>
      </div>
    </footer>
  );
}

