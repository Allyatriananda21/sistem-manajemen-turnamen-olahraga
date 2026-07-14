import React, { useState } from 'react';
import { motion } from 'motion/react';
import { Calendar, RefreshCw, Flame, Radio, CheckCircle2, XCircle, LayoutList } from 'lucide-react';
import useFetch from '../hooks/useFetch';
import { SkeletonCard } from '../components/LoadingSkeleton';
import EmptyState from '../components/EmptyState';
import ErrorState from '../components/ErrorState';
import MatchCard, { Match } from '../components/MatchCard';

type TabStatus = 'all' | 'ongoing' | 'scheduled' | 'done' | 'cancelled';

export default function Matches() {
  const [activeTab, setActiveTab] = useState<TabStatus>('all');
  const [selectedRound, setSelectedRound] = useState<string>('all');

  const { data: response, loading, error, refetch } = useFetch<{ data: Match[] }>('/matches');

  // Extract matches list safely
  const matches: Match[] = response?.data || (Array.isArray(response) ? (response as any) : []);

  // Dynamically extract distinct round/stage names from the retrieved data
  const rounds = ['all', ...Array.from(new Set(matches.map((m) => m.round))).filter(Boolean)];

  // Filter matches based on status tab and selected round dropdown
  const filteredMatches = matches.filter((match) => {
    const statusMatches = activeTab === 'all' || match.status === activeTab;
    const roundMatches = selectedRound === 'all' || match.round === selectedRound;
    return statusMatches && roundMatches;
  });

  // Extract all currently ongoing (LIVE) matches for high-visibility top billing
  const liveMatches = matches.filter((m) => m.status === 'ongoing');

  const tabItems: { label: React.ReactNode; value: TabStatus; color: string }[] = [
    { label: <span className="flex items-center gap-1.5"><LayoutList className="w-4 h-4" /> Semua Laga</span>, value: 'all', color: 'bg-white/10' },
    { label: <span className="flex items-center gap-1.5"><Radio className="w-4 h-4" /> LIVE</span>, value: 'ongoing', color: 'bg-red-500/10 text-red-400' },
    { label: <span className="flex items-center gap-1.5"><Calendar className="w-4 h-4" /> Terjadwal</span>, value: 'scheduled', color: 'bg-blue-500/10 text-blue-400' },
    { label: <span className="flex items-center gap-1.5"><CheckCircle2 className="w-4 h-4" /> Selesai</span>, value: 'done', color: 'bg-emerald-500/10 text-emerald-400' },
    { label: <span className="flex items-center gap-1.5"><XCircle className="w-4 h-4" /> Batal</span>, value: 'cancelled', color: 'bg-slate-500/10 text-slate-400' },
  ];

  // Map active tabs to user-friendly indonesian labels for EmptyStates
  const getTabLabel = (status: TabStatus) => {
    switch (status) {
      case 'ongoing': return 'Sedang Berlangsung';
      case 'scheduled': return 'Terjadwal';
      case 'done': return 'Selesai';
      case 'cancelled': return 'Dibatalkan';
      default: return '';
    }
  };

  return (
    <div id="matches-page" className="relative min-h-screen bg-transparent pt-28 pb-20 px-4 sm:px-6 lg:px-8">
      {/* Decorative Ornaments */}
      <div className="absolute top-10 left-10 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none" />
      <div className="absolute bottom-20 right-10 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px] pointer-events-none" />

      <div className="max-w-7xl mx-auto space-y-10 relative z-10">
        
        {/* Header */}
        <div className="text-center space-y-3 max-w-2xl mx-auto">
          <span className="text-xs font-mono font-bold tracking-wider text-[#E4FD97] uppercase flex items-center justify-center gap-1.5">
            <Calendar className="w-4 h-4 text-[#E4FD97]" />
            Jadwal & Hasil
          </span>
          <h1 className="font-display text-3xl sm:text-4xl font-extrabold uppercase text-white tracking-tight">
            AGENDA PERTANDINGAN
          </h1>
          <p className="text-slate-400 text-sm">
            Pantau seluruh jadwal pertandingan yang akan datang, hasil skor akhir, dan saksikan pertandingan yang sedang berlangsung secara langsung.
          </p>
        </div>

        {/* Section Highlight: LIVE PINNED ONGOING MATCHES ON TOP */}
        {!loading && !error && liveMatches.length > 0 && (
          <div className="space-y-4">
            <div className="flex items-center gap-2">
              <span className="relative flex h-3 w-3">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span className="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
              </span>
              <h3 className="font-display font-bold text-sm tracking-wider uppercase text-red-500 flex items-center gap-1.5">
                PERTANDINGAN SEDANG BERLANGSUNG ({liveMatches.length})
              </h3>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {liveMatches.map((match, idx) => (
                <MatchCard key={`live-${match.id}`} match={match} index={idx} />
              ))}
            </div>
          </div>
        )}

        {/* Filters Controls & Status Tabs */}
        <div className="glass-card p-4 sm:p-6 rounded-2xl border border-white/5 space-y-5 shadow-2xl">
          <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            {/* Status tabs */}
            <div className="flex flex-wrap items-center gap-2 overflow-x-auto pb-1 lg:pb-0 scrollbar-none">
              {tabItems.map((tab) => (
                <button
                  key={tab.value}
                  onClick={() => setActiveTab(tab.value)}
                  className={`px-4 py-2.5 rounded-xl font-medium text-xs sm:text-sm tracking-wide transition-all whitespace-nowrap border ${
                    activeTab === tab.value
                      ? 'bg-[#E4FD97] text-[#2D3E2C] border-[#E4FD97] font-bold shadow-[0_0_15px_rgba(228,253,151,0.3)]'
                      : 'bg-white/5 hover:bg-white/10 text-slate-300 border-white/5 cursor-pointer'
                  }`}
                >
                  {tab.label}
                </button>
              ))}
            </div>

            {/* Dropdown round filters */}
            <div className="flex flex-wrap items-center gap-3 shrink-0">
              <label htmlFor="round-filter" className="text-xs font-bold font-mono tracking-wider text-slate-400 uppercase whitespace-nowrap">
                BABAK / ROUND:
              </label>
              <select
                id="round-filter"
                value={selectedRound}
                onChange={(e) => setSelectedRound(e.target.value)}
                className="bg-[#2D3E2C]/80 border border-white/10 rounded-xl py-2 px-4 text-xs sm:text-sm text-white focus:outline-none focus:border-[#E4FD97]/50 focus:ring-1 focus:ring-[#E4FD97]/30 transition-all cursor-pointer min-w-[150px] [&>option]:bg-dark-bg [&>option]:text-white"
              >
                <option value="all">Semua Babak</option>
                {rounds.filter(r => r !== 'all').map((r) => (
                  <option key={r} value={r}>
                    {r}
                  </option>
                ))}
              </select>
              
              <button
                onClick={() => refetch()}
                className="p-2.5 bg-white/5 hover:bg-white/10 border border-white/10 text-white rounded-xl transition-all cursor-pointer"
                title="Refresh Jadwal"
              >
                <RefreshCw className="w-4 h-4 text-slate-300 animate-spin-hover" />
              </button>
            </div>
          </div>
        </div>

        {/* Dynamic States */}
        {loading ? (
          /* Show exactly 4 SkeletonCards when loading */
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3, 4].map((i) => (
              <SkeletonCard key={i} />
            ))}
          </div>
        ) : error ? (
          <ErrorState message={error} onRetry={() => refetch()} />
        ) : filteredMatches.length === 0 ? (
          /* Empty state for the specific active filter selection */
          <EmptyState
            icon={<Calendar size={32} />}
            title="Tidak ada pertandingan"
            description={
              activeTab !== 'all'
                ? `Tidak ada pertandingan dengan status ${getTabLabel(activeTab)} saat ini.`
                : 'Tidak ada jadwal pertandingan terdaftar yang cocok.'
            }
          />
        ) : (
          /* Match Schedule Grid */
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredMatches.map((match, index) => (
              <MatchCard key={match.id} match={match} index={index} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
