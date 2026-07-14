import React, { useEffect, useState, useRef } from 'react';
import { Link } from 'react-router-dom';
import { motion, useInView, useScroll, useTransform } from 'motion/react';
import { Trophy, Users, Calendar, BarChart3, HelpCircle, ArrowRight, Flame, Sparkles, AlertCircle, RefreshCw, Star, ArrowUpRight, Shield, Award, CheckCircle2, ChevronDown } from 'lucide-react';
import useFetch from '../hooks/useFetch';
import ElaraReveal from '../components/ElaraReveal';

// Types for backend entities
interface Team {
  id: number;
  name: string;
  sport_type: string;
}

interface Match {
  id: number;
  round: string;
  status: 'scheduled' | 'ongoing' | 'done' | 'cancelled';
  team1: { id: number; name: string };
  team2: { id: number; name: string };
}

// Custom Counter Component for Smooth Number Roll-up Animation
function AnimatedCounter({ value, duration = 1.2 }: { value: number | string; duration?: number }) {
  const [count, setCount] = useState<number>(0);
  const elementRef = useRef<HTMLSpanElement>(null);
  const isInView = useInView(elementRef, { once: true });

  useEffect(() => {
    if (!isInView || typeof value !== 'number') {
      return;
    }

    let start = 0;
    const end = value;
    if (start === end) {
      setCount(end);
      return;
    }

    const totalMs = duration * 1000;
    const stepTime = Math.max(Math.floor(totalMs / end), 15);
    
    const timer = setInterval(() => {
      start += Math.ceil(end / (totalMs / stepTime));
      if (start >= end) {
        clearInterval(timer);
        setCount(end);
      } else {
        setCount(start);
      }
    }, stepTime);

    return () => clearInterval(timer);
  }, [value, duration, isInView]);

  if (typeof value !== 'number') {
    return <span ref={elementRef}>{value}</span>;
  }

  return <span ref={elementRef}>{count}</span>;
}

export default function Home() {
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(null);

  const regulations = [
    {
      title: "Sistem Kompetisi Profesional",
      desc: "Kombinasi fase grup berformat liga poin disusul fase Knockout dengan bagan gugur otomatis bagi tim-tim terbaik.",
      icon: Trophy,
      color: "text-amber-400 bg-amber-400/10 border-amber-400/20"
    },
    {
      title: "Fair Play & Kedisiplinan",
      desc: "Menghargai sportivitas dengan sistem pencatatan kartu ketat dan sanksi diskualifikasi bagi tim yang melanggar kode etik.",
      icon: Shield,
      color: "text-[#E4FD97] bg-[#E4FD97]/10 border-[#E4FD97]/20"
    },
    {
      title: "Pendaftaran Skuad Resmi",
      desc: "Setiap tim terdaftar wajib memiliki manajer aktif, susunan 11 pemain inti, serta maksimal 7 pemain cadangan.",
      icon: Users,
      color: "text-blue-400 bg-blue-500/10 border-blue-500/20"
    },
    {
      title: "Teknologi Pencatatan Skor",
      desc: "Seluruh hasil laga, pencetak gol, dan klasemen dihitung secara otomatis menggunakan mesin kalkulasi liga real-time.",
      icon: CheckCircle2,
      color: "text-purple-400 bg-purple-500/10 border-purple-500/20"
    }
  ];

  const faqItems = [
    {
      question: "Bagaimana cara mendaftarkan tim baru?",
      answer: "Buka menu 'Registrasi' di navigasi atas atau klik tombol 'Daftarkan Tim Kamu' di halaman beranda. Lengkapi seluruh informasi mulai dari nama tim, jenis cabang olahraga, kontak manajer, hingga berkas logo tim. Skuad Anda akan segera terdata secara otomatis."
    },
    {
      question: "Bagaimana sistem penghitungan poin klasemen?",
      answer: "Poin klasemen dihitung berdasarkan hasil pertandingan: Menang mendapatkan 3 poin, Seri (Draw) mendapatkan 1 poin, dan Kalah mendapatkan 0 poin. Peringkat diurutkan berdasarkan total poin, selisih gol (selisih skor masuk dan kebobolan), lalu produktivitas gol."
    },
    {
      question: "Kapan jadwal babak sistem gugur dirilis?",
      answer: "Bagan Bracket (sistem gugur) akan diperbarui secara otomatis setelah seluruh rangkaian pertandingan pada fase liga selesai dimainkan. Posisi tim pada bracket diatur berdasarkan performa akhir mereka di papan klasemen utama."
    },
    {
      question: "Apakah skor pertandingan diperbarui secara real-time?",
      answer: "Ya! Setiap gol atau perubahan skor yang diperbarui oleh admin akan langsung tercermin secara real-time di halaman 'Jadwal & Hasil' serta memengaruhi perubahan klasemen sementara secara langsung."
    }
  ];

  // Fetch real-time data from APIs to calculate dashboard landing stats
  const { data: teamsResponse, loading: loadingTeams, error: errorTeams } = useFetch<{ data: Team[] }>('/teams');
  const { data: matchesResponse, loading: loadingMatches, error: errorMatches } = useFetch<{ data: Match[] }>('/matches');

  // Derive stats with fallback to placeholder '-' if there's an error
  const hasError = !!(errorTeams || errorMatches);
  const isLoading = loadingTeams || loadingMatches;

  const totalTeams = teamsResponse?.data ? teamsResponse.data.length : (hasError ? '-' : 0);
  const totalMatches = matchesResponse?.data ? matchesResponse.data.length : (hasError ? '-' : 0);
  const liveMatches = matchesResponse?.data 
    ? matchesResponse.data.filter((m) => m.status === 'ongoing').length 
    : (hasError ? '-' : 0);

  // Parallax scroll effects setup
  const heroRef = useRef<HTMLDivElement>(null);
  const { scrollYProgress } = useScroll({
    target: heroRef,
    offset: ["start start", "end start"]
  });

  const yText = useTransform(scrollYProgress, [0, 1], ["0%", "50%"]);
  const yBg = useTransform(scrollYProgress, [0, 1], ["0%", "30%"]);
  const opacityHero = useTransform(scrollYProgress, [0, 1], [1, 0]);
  const scaleHero = useTransform(scrollYProgress, [0, 1], [1, 0.90]);

  // Floating decorative items moving at different custom speeds
  const yFloatLeft = useTransform(scrollYProgress, [0, 1], [0, -140]);
  const yFloatRight = useTransform(scrollYProgress, [0, 1], [0, 90]);
  const rotateFloatLeft = useTransform(scrollYProgress, [0, 1], [0, -25]);
  const rotateFloatRight = useTransform(scrollYProgress, [0, 1], [0, 30]);

  return (
    <div id="home-page" className="relative min-h-screen bg-transparent pt-24 pb-20 overflow-hidden">
      {/* Decorative Ornaments and Atmospheric Glows */}
      <motion.div 
        style={{ y: yBg }}
        className="absolute top-0 left-1/4 w-[400px] h-[400px] bg-emerald-500/10 rounded-full blur-[130px] pointer-events-none z-0" 
      />
      <motion.div 
        style={{ y: yBg }}
        className="absolute top-1/3 right-10 w-[350px] h-[350px] bg-blue-500/10 rounded-full blur-[120px] pointer-events-none z-0" 
      />

      {/* Hero Section */}
      <section ref={heroRef} className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-20 z-10 min-h-[85vh] flex items-center justify-center">
        {/* Floating Decorative Parallax Badges */}
        <motion.div
          style={{ y: yFloatLeft, rotate: rotateFloatLeft }}
          className="hidden md:flex absolute left-4 lg:left-12 top-12 items-center gap-3 px-6 py-3 bg-white/5 border border-white/10 rounded-3xl text-sm md:text-base text-[#E4FD97] shadow-[0_12px_24px_rgba(0,0,0,0.4)] backdrop-blur-md z-20 font-mono font-bold"
        >
          <Trophy className="w-5 h-5 text-[#E4FD97]" />
          <span>#1 LEAGUE PLATFORM</span>
        </motion.div>

        <motion.div
          style={{ y: yFloatRight, rotate: rotateFloatRight }}
          className="hidden md:flex absolute right-4 lg:right-16 bottom-12 items-center gap-3 px-6 py-3 bg-[#E4FD97]/10 border border-[#E4FD97]/20 rounded-3xl text-sm md:text-base text-[#E4FD97] shadow-[0_12px_24px_rgba(0,0,0,0.4)] backdrop-blur-md z-20 font-mono font-bold"
        >
          <Flame className="w-5 h-5 text-orange-400 animate-pulse" />
          <span>MATCHDAY LIVE</span>
        </motion.div>

        <motion.div 
          style={{ y: yText, opacity: opacityHero, scale: scaleHero }}
          className="max-w-4xl mx-auto text-center space-y-6"
        >
          <motion.div 
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-xs text-[#E4FD97]"
          >
            <Sparkles className="w-3.5 h-3.5 text-[#E4FD97] animate-pulse" />
            <span>CHAMPIONSHIP SERIES • LIVE BRACKET & STATS</span>
          </motion.div>

          <motion.h1 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.15 }}
            className="font-display text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold tracking-tight leading-none text-white uppercase"
          >
            TURNAMEN <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#E4FD97] to-emerald-400 text-glow-white italic pr-4">
              OLAHRAGA 2026
            </span>
          </motion.h1>

          <motion.p 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.25 }}
            className="text-slate-300 text-sm sm:text-base md:text-lg max-w-2xl mx-auto leading-relaxed font-sans"
          >
            Sambut gelombang energi persaingan olahraga paling bergengsi musim ini! Pantau tim terbaik, dapatkan pembaruan jadwal instan, hitung poin klasemen secara dinamis, dan saksi dari pertarungan puncak bagan gugur.
          </motion.p>

          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.35 }}
            className="flex flex-col sm:flex-row items-center justify-center gap-4 pt-3"
          >
            <Link 
              to="/register" 
              className="flex items-center gap-2 w-full sm:w-auto justify-center px-8 py-4 bg-[#E4FD97] text-[#2D3E2C] font-display font-bold rounded-xl text-sm transition-all duration-300 hover:bg-[#E4FD97]/95 hover:scale-105 shadow-[0_0_20px_rgba(228,253,151,0.3)] active:scale-95"
            >
              Daftarkan Tim Kamu
              <ArrowRight className="w-4 h-4" />
            </Link>
            <Link 
              to="/teams" 
              className="flex items-center gap-2 w-full sm:w-auto justify-center px-8 py-4 bg-white/5 border border-white/10 text-white font-display font-semibold rounded-xl text-sm transition-all duration-300 hover:bg-white/10 hover:border-white/20 active:scale-95"
            >
              Jelajahi Kontestan
            </Link>
          </motion.div>
        </motion.div>
      </section>

      {/* Summary Statistics Section */}
      <div className="w-full bg-white/[0.02] border-y border-white/10 mt-10">
        <section className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 z-10">
          <motion.div 
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-50px" }}
          transition={{ duration: 0.6 }}
          className="grid grid-cols-1 sm:grid-cols-3 gap-6"
        >
          {/* Stat 1: Total Teams */}
          <div className="glass-card p-6 sm:p-8 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-[#E4FD97]/20 transition-all duration-300">
            <div className="absolute top-4 right-4 text-[#E4FD97]/10 group-hover:text-[#E4FD97]/20 group-hover:scale-110 transition-all duration-300">
              <Users size={48} />
            </div>
            {isLoading ? (
              <div className="space-y-3 animate-pulse">
                <div className="h-8 bg-white/5 rounded w-16" />
                <div className="h-4 bg-white/5 rounded w-24" />
              </div>
            ) : (
              <>
                <div className="font-mono text-4xl sm:text-5xl font-extrabold text-[#E4FD97] leading-none tracking-tight">
                  <AnimatedCounter value={totalTeams} />
                </div>
                <div className="font-display font-bold text-sm text-white mt-3 uppercase tracking-wider">
                  Tim Peserta
                </div>
                <p className="text-xs text-slate-400 mt-1">
                  Skuad tangguh siap berlaga merebut podium tertinggi.
                </p>
              </>
            )}
          </div>

          {/* Stat 2: Total Matches */}
          <div className="glass-card p-6 sm:p-8 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-[#E4FD97]/20 transition-all duration-300">
            <div className="absolute top-4 right-4 text-[#E4FD97]/10 group-hover:text-[#E4FD97]/20 group-hover:scale-110 transition-all duration-300">
              <Calendar size={48} />
            </div>
            {isLoading ? (
              <div className="space-y-3 animate-pulse">
                <div className="h-8 bg-white/5 rounded w-16" />
                <div className="h-4 bg-white/5 rounded w-24" />
              </div>
            ) : (
              <>
                <div className="font-mono text-4xl sm:text-5xl font-extrabold text-[#E4FD97] leading-none tracking-tight">
                  <AnimatedCounter value={totalMatches} />
                </div>
                <div className="font-display font-bold text-sm text-white mt-3 uppercase tracking-wider">
                  Pertandingan
                </div>
                <p className="text-xs text-slate-400 mt-1">
                  Total jadwal laga seru dari penyisihan hingga gugur.
                </p>
              </>
            )}
          </div>

          {/* Stat 3: Live Matches */}
          <div className="glass-card p-6 sm:p-8 rounded-2xl border border-white/5 relative overflow-hidden group hover:border-[#22C55E]/20 transition-all duration-300">
            <div className="absolute top-4 right-4 text-red-500/10 group-hover:text-red-500/20 group-hover:scale-110 transition-all duration-300">
              <Flame className="w-12 h-12 text-red-500" />
            </div>
            {isLoading ? (
              <div className="space-y-3 animate-pulse">
                <div className="h-8 bg-white/5 rounded w-16" />
                <div className="h-4 bg-white/5 rounded w-24" />
              </div>
            ) : (
              <>
                <div className="font-mono text-4xl sm:text-5xl font-extrabold text-red-400 leading-none tracking-tight flex items-center gap-2">
                  <AnimatedCounter value={liveMatches} />
                  {typeof liveMatches === 'number' && liveMatches > 0 && (
                    <span className="w-3 h-3 bg-red-500 rounded-full animate-ping" />
                  )}
                </div>
                <div className="font-display font-bold text-sm text-white mt-3 uppercase tracking-wider">
                  Sedang Berlangsung
                </div>
                <p className="text-xs text-slate-400 mt-1">
                  Laga panas live detik ini yang memperebutkan poin penting.
                </p>
              </>
            )}
          </div>
        </motion.div>
      </section>
      </div>

      {/* Quick Navigation Cards */}
      <section className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 z-10">
        <motion.div 
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          className="text-center mb-12 space-y-2"
        >
          <span className="text-xs font-mono font-bold tracking-wider text-[#E4FD97] uppercase">Quick Access</span>
          <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-white uppercase">
            Akses Cepat Turnamen
          </h2>
          <p className="text-slate-400 text-sm max-w-xl mx-auto font-sans leading-relaxed">
            Dapatkan informasi lengkap dari satu dashboard terpadu. Klik pintasan di bawah untuk menjelajah.
          </p>
        </motion.div>

        <motion.div 
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-50px" }}
          transition={{ duration: 0.6, staggerChildren: 0.1 }}
          className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
        >
          {[
            {
              title: 'Tim Kontestan',
              desc: 'Lihat profil seluruh tim peserta, periksa lisensi, status verifikasi, dan manajer tim.',
              path: '/teams',
              icon: Users,
              color: 'group-hover:text-blue-400',
              bgColor: 'group-hover:bg-blue-500/10'
            },
            {
              title: 'Jadwal & Hasil',
              desc: 'Cari tanggal tanding, periksa hasil skor akhir, dan saksikan pertandingan langsung.',
              path: '/matches',
              icon: Calendar,
              color: 'group-hover:text-[#E4FD97]',
              bgColor: 'group-hover:bg-[#E4FD97]/10'
            },
            {
              title: 'Klasemen Turnamen',
              desc: 'Pantau akumulasi poin, selisih gol, rekor kemenangan, dan statistik performa tim.',
              path: '/standings',
              icon: BarChart3,
              color: 'group-hover:text-yellow-400',
              bgColor: 'group-hover:bg-yellow-500/10'
            },
            {
              title: 'Bagan Bracket',
              desc: 'Masuk fase gugur, saksikan visual bagan bracket knockout otomatis s/d juara.',
              path: '/bracket',
              icon: HelpCircle,
              color: 'group-hover:text-purple-400',
              bgColor: 'group-hover:bg-purple-500/10'
            }
          ].map((nav, index) => (
            <Link 
              key={index} 
              to={nav.path}
              className="glass-card p-6 sm:p-7 rounded-2xl border border-white/5 flex flex-col justify-between group hover:border-white/10 hover:shadow-[0_12px_30px_rgba(0,0,0,0.4)] transition-all duration-300 relative overflow-hidden"
            >
              {/* Corner Accent Arrow Icon */}
              <div className="absolute top-4 right-4 text-slate-500 group-hover:text-white transition-colors duration-300">
                <ArrowUpRight size={18} className="transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
              </div>

              <div>
                <div className={`w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 ${nav.color} ${nav.bgColor} transition-all duration-300 mb-6`}>
                  <nav.icon size={22} />
                </div>
                <h3 className="font-display font-bold text-white text-base tracking-tight mb-2 group-hover:text-glow-white transition-colors">
                  {nav.title}
                </h3>
                <p className="text-xs text-slate-400 leading-relaxed font-sans mb-6">
                  {nav.desc}
                </p>
              </div>

              <div className="flex items-center gap-1.5 text-xs font-semibold text-[#E4FD97] group-hover:underline">
                Buka Halaman
                <ArrowRight size={12} className="transform group-hover:translate-x-1 transition-transform" />
              </div>
            </Link>
          ))}
        </motion.div>
      </section>

      {/* Elara Reveal Section */}
      <ElaraReveal />

      {/* Tournament Regulations Section */}
      <section className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 z-10 border-t border-white/5">
        <motion.div 
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          className="text-center mb-12 space-y-2"
        >
          <span className="text-xs font-mono font-bold tracking-wider text-[#E4FD97] uppercase">Aturan Main</span>
          <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-white uppercase">
            Format & Regulasi Turnamen
          </h2>
          <p className="text-slate-400 text-sm max-w-xl mx-auto font-sans leading-relaxed">
            Standar regulasi resmi yang diterapkan untuk memastikan persaingan yang sehat, transparan, dan menjunjung tinggi sportivitas.
          </p>
        </motion.div>

        <motion.div 
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-50px" }}
          transition={{ duration: 0.6 }}
          className="grid grid-cols-1 md:grid-cols-2 gap-6"
        >
          {regulations.map((item, idx) => (
            <div 
              key={idx}
              className="glass-card p-6 rounded-2xl border border-white/5 flex gap-4 hover:border-white/10 transition-all duration-300 group"
            >
              <div className={`w-12 h-12 rounded-xl border flex items-center justify-center shrink-0 ${item.color} transition-all duration-300 group-hover:scale-105`}>
                <item.icon size={22} />
              </div>
              <div className="space-y-1.5">
                <h3 className="font-display font-bold text-white text-base tracking-tight group-hover:text-[#E4FD97] transition-colors">
                  {item.title}
                </h3>
                <p className="text-xs text-slate-400 leading-relaxed font-sans">
                  {item.desc}
                </p>
              </div>
            </div>
          ))}
        </motion.div>
      </section>

      {/* Interactive FAQ Section */}
      <section className="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 z-10 border-t border-white/5">
        <motion.div 
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          className="text-center mb-12 space-y-2"
        >
          <span className="text-xs font-mono font-bold tracking-wider text-[#E4FD97] uppercase">Pusat Bantuan</span>
          <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-white uppercase">
            Tanya Jawab Populer
          </h2>
          <p className="text-slate-400 text-sm max-w-xl mx-auto font-sans leading-relaxed">
            Punya pertanyaan mengenai turnamen? Temukan jawaban cepat seputar pendaftaran, klasemen, dan sistem pertandingan.
          </p>
        </motion.div>

        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-50px" }}
          transition={{ duration: 0.5 }}
          className="space-y-4"
        >
          {faqItems.map((faq, idx) => {
            const isOpen = openFaqIndex === idx;
            return (
              <div 
                key={idx}
                className={`glass-card rounded-2xl border transition-all duration-300 overflow-hidden ${
                  isOpen ? 'border-[#E4FD97]/30 bg-[#E4FD97]/[0.02]' : 'border-white/5 hover:border-white/10'
                }`}
              >
                <button
                  onClick={() => setOpenFaqIndex(isOpen ? null : idx)}
                  className="w-full text-left p-5 sm:p-6 flex items-center justify-between gap-4 cursor-pointer focus:outline-none"
                >
                  <span className={`font-display font-bold text-sm sm:text-base tracking-tight transition-colors ${
                    isOpen ? 'text-[#E4FD97]' : 'text-white'
                  }`}>
                    {faq.question}
                  </span>
                  <div className={`w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 transition-all duration-300 shrink-0 ${
                    isOpen ? 'rotate-180 bg-[#E4FD97]/10 text-[#E4FD97]' : ''
                  }`}>
                    <ChevronDown size={16} />
                  </div>
                </button>

                <div 
                  className={`transition-all duration-300 ease-in-out overflow-hidden ${
                    isOpen ? 'max-h-[300px] border-t border-white/5' : 'max-h-0'
                  }`}
                >
                  <div className="p-5 sm:p-6 text-xs sm:text-sm text-slate-300 leading-relaxed font-sans">
                    {faq.answer}
                  </div>
                </div>
              </div>
            );
          })}
        </motion.div>
      </section>
    </div>
  );
}
