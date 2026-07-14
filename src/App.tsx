import React from 'react';
import { Routes, Route, useLocation } from 'react-router-dom';
import { motion, AnimatePresence } from 'motion/react';
import Navbar from './components/Navbar';
import Footer from './components/Footer';

// Pages Import
import Home from './pages/Home';
import Teams from './pages/Teams';
import Register from './pages/Register';
import Matches from './pages/Matches';
import Standings from './pages/Standings';
import Bracket from './pages/Bracket';

export default function App() {
  const location = useLocation();

  React.useEffect(() => {
    const getPageName = (path: string) => {
      switch (path) {
        case '/': return 'Beranda';
        case '/teams': return 'Tim';
        case '/register': return 'Daftar Tim';
        case '/matches': return 'Jadwal';
        case '/standings': return 'Klasemen';
        case '/bracket': return 'Bracket';
        default: return 'Beranda';
      }
    };
    
    document.title = `TrophyHub - ${getPageName(location.pathname)}`;
  }, [location.pathname]);

  return (
    <div className="flex flex-col min-h-screen bg-[#0F172A] text-slate-100 font-sans selection:bg-accent selection:text-[#0F172A] relative overflow-hidden">
      {/* Background Mesh Gradients for Frosted Glass theme */}
      <div className="absolute top-[-10%] left-[-10%] w-[60vw] h-[60vw] max-w-[800px] max-h-[800px] rounded-full bg-[#1E3A5F] opacity-35 blur-[130px] pointer-events-none z-0"></div>
      <div className="absolute bottom-[-10%] right-[-10%] w-[60vw] h-[60vw] max-w-[800px] max-h-[800px] rounded-full bg-[#22C55E] opacity-15 blur-[130px] pointer-events-none z-0"></div>
      <div className="absolute top-[40%] left-[20%] w-[40vw] h-[40vw] rounded-full bg-[#1E3A5F]/20 blur-[120px] pointer-events-none z-0"></div>

      {/* Navigation Bar */}
      <div className="relative z-50">
        <Navbar />
      </div>

      {/* Main Content Area with Page transitions */}
      <main className="flex-grow relative z-10">
        <AnimatePresence mode="wait">
          <Routes location={location}>
            <Route 
              path="/" 
              element={
                <PageWrapper>
                  <Home />
                </PageWrapper>
              } 
            />
            <Route 
              path="/teams" 
              element={
                <PageWrapper>
                  <Teams />
                </PageWrapper>
              } 
            />
            <Route 
              path="/register" 
              element={
                <PageWrapper>
                  <Register />
                </PageWrapper>
              } 
            />
            <Route 
              path="/matches" 
              element={
                <PageWrapper>
                  <Matches />
                </PageWrapper>
              } 
            />
            <Route 
              path="/standings" 
              element={
                <PageWrapper>
                  <Standings />
                </PageWrapper>
              } 
            />
            <Route 
              path="/bracket" 
              element={
                <PageWrapper>
                  <Bracket />
                </PageWrapper>
              } 
            />
            {/* Catch-all route to redirect back Home */}
            <Route 
              path="*" 
              element={
                <PageWrapper>
                  <Home />
                </PageWrapper>
              } 
            />
          </Routes>
        </AnimatePresence>
      </main>

      {/* Footer Area */}
      <div className="relative z-10">
        <Footer />
      </div>
    </div>
  );
}

// Reusable transition wrapper for smooth entry animations
function PageWrapper({ children }: { children: React.ReactNode }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -10 }}
      transition={{ duration: 0.35, ease: 'easeInOut' }}
    >
      {children}
    </motion.div>
  );
}
