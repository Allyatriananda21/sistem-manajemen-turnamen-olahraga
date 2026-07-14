import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Trophy, Menu, X, PlusCircle, Users, Calendar, BarChart3, HelpCircle } from 'lucide-react';

export default function Navbar() {
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const location = useLocation();

  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        setScrolled(true);
      } else {
        setScrolled(false);
      }
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navLinks = [
    { name: 'Beranda', path: '/', icon: Trophy },
    { name: 'Tim', path: '/teams', icon: Users },
    { name: 'Jadwal', path: '/matches', icon: Calendar },
    { name: 'Klasemen', path: '/standings', icon: BarChart3 },
    { name: 'Bracket', path: '/bracket', icon: HelpCircle },
  ];

  return (
    <nav
      id="main-navbar"
      className={`fixed top-0 left-0 w-full z-50 transition-all duration-300 ${
        isOpen
          ? 'bg-[#2D3E2C] border-b border-white/10 py-3 shadow-lg'
          : scrolled
            ? 'bg-[#2D3E2C]/90 backdrop-blur-md border-b border-white/10 py-3 shadow-[0_4px_30px_rgba(0,0,0,0.35)]'
            : 'bg-transparent py-5'
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-12">
          {/* Logo & Branding */}
          <Link 
            to="/" 
            onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
            className="flex items-center gap-2 group"
          >
            <span className="font-display font-bold text-lg md:text-xl tracking-tight text-white group-hover:text-[#E4FD97] transition-colors flex items-center gap-2.5">
              <Trophy className="w-6 h-6 text-[#E4FD97] group-hover:scale-110 transition-transform duration-300" /> TrophyHub
            </span>
          </Link>
 
          {/* Desktop Navigation */}
          <div className="hidden lg:flex items-center gap-1">
            {navLinks.map((link) => {
              const Icon = link.icon;
              const isActive = location.pathname === link.path;
              return (
                <Link
                  key={link.path}
                  to={link.path}
                  className={`flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 ${
                    isActive
                      ? 'text-[#E4FD97] bg-[#E4FD97]/10 border border-[#E4FD97]/20'
                      : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent'
                  }`}
                >
                  {link.name}
                </Link>
              );
            })}
          </div>
 
          {/* CTA Button */}
          <div className="hidden lg:block">
            <Link
              to="/register"
              className="flex items-center gap-2 px-5 py-2.5 bg-[#E4FD97] text-[#2D3E2C] font-display font-semibold rounded-lg text-sm transition-all duration-200 hover:bg-[#E4FD97]/80 hover:scale-105 shadow-[0_0_15px_rgba(228,253,151,0.3)] hover:shadow-[0_0_25px_rgba(228,253,151,0.5)]"
            >
              <PlusCircle className="w-4 h-4" />
              Daftar Tim
            </Link>
          </div>
 
          {/* Mobile hamburger menu */}
          <div className="lg:hidden">
            <button
              onClick={() => setIsOpen(!isOpen)}
              className="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-white transition-all animate-active"
              aria-label="Toggle menu"
            >
              {isOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>
      </div>
 
      {/* Mobile Drawer */}
      <div
        className={`lg:hidden fixed top-[72px] right-0 bottom-0 left-0 bg-[#2D3E2C] z-40 transition-all duration-300 transform border-t border-white/5 overflow-y-auto ${
          isOpen ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0 pointer-events-none'
        }`}
      >
        <div className="px-4 py-6 space-y-3">
          {navLinks.map((link) => {
            const Icon = link.icon;
            const isActive = location.pathname === link.path;
            return (
              <Link
                key={link.path}
                to={link.path}
                onClick={() => setIsOpen(false)}
                className={`flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium text-base transition-all ${
                  isActive
                    ? 'text-[#E4FD97] bg-[#E4FD97]/10 border border-[#E4FD97]/20'
                    : 'text-slate-300 hover:text-white hover:bg-white/5 border border-transparent'
                }`}
              >
                {link.name}
              </Link>
            );
          })}
          <div className="pt-4 border-t border-white/5">
            <Link
              to="/register"
              onClick={() => setIsOpen(false)}
              className="flex items-center justify-center gap-2 w-full py-3 bg-[#E4FD97] text-[#2D3E2C] font-display font-semibold rounded-xl transition-all hover:bg-[#E4FD97]/80 shadow-lg"
            >
              <PlusCircle className="w-5 h-5" />
              Daftar Tim Sekarang
            </Link>
          </div>
        </div>
      </div>
    </nav>
  );
}
