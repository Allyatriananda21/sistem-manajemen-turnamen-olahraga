import React, { useState } from 'react';
import { motion } from 'motion/react';
import { Users, Search, RefreshCw } from 'lucide-react';
import useFetch from '../hooks/useFetch';
import { SkeletonCard } from '../components/LoadingSkeleton';
import EmptyState from '../components/EmptyState';
import ErrorState from '../components/ErrorState';
import TeamCard, { Team } from '../components/TeamCard';

export default function Teams() {
  const [searchQuery, setSearchQuery] = useState('');
  const { data: response, loading, error, refetch } = useFetch<{ data: Team[] }>('/teams');

  // Extract array of teams. Robust to both { data: [...] } and directly [...]
  const teams: Team[] = response?.data || (Array.isArray(response) ? (response as any) : []);

  // Client-side search filtering by name
  const filteredTeams = teams.filter((team) =>
    team.name.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div id="teams-page" className="relative min-h-screen bg-transparent pt-28 pb-20 px-4 sm:px-6 lg:px-8">
      {/* Background Decorator Ornaments */}
      <div className="absolute top-10 right-10 w-72 h-72 bg-emerald-500/5 rounded-full blur-[80px] pointer-events-none" />
      <div className="absolute bottom-10 left-10 w-72 h-72 bg-blue-500/5 rounded-full blur-[80px] pointer-events-none" />

      <div className="max-w-7xl mx-auto space-y-10 relative z-10">
        
        {/* Header Section */}
        <div className="text-center space-y-3 max-w-2xl mx-auto">
          <span className="text-xs font-mono font-bold tracking-wider text-[#E4FD97] uppercase flex items-center justify-center gap-1.5">
            <Users className="w-4 h-4 text-[#E4FD97]" />
            Roster Resmi
          </span>
          <h1 className="font-display text-3xl sm:text-4xl font-extrabold uppercase text-white tracking-tight">
            TIM PESERTA TERVERIFIKASI
          </h1>
          <p className="text-slate-400 text-sm">
            Daftar seluruh tim tangguh yang siap berlaga memperebutkan tahta tertinggi. Cari dan periksa tim favoritmu di bawah ini.
          </p>
        </div>

        {/* Filter Controls & Search */}
        <div className="glass-card p-4 sm:p-6 rounded-2xl flex flex-col md:flex-row items-center gap-4 border border-white/5 shadow-xl">
          <div className="relative w-full md:flex-1">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
            <input
              type="text"
              placeholder="Cari tim berdasarkan nama..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full bg-[#2D3E2C]/60 border border-white/10 rounded-xl py-3 pl-12 pr-4 text-sm text-white placeholder-slate-400 focus:outline-none focus:border-[#E4FD97]/50 focus:ring-1 focus:ring-[#E4FD97]/30 transition-all font-sans"
            />
          </div>
          <button
            onClick={() => refetch()}
            className="flex items-center gap-2 justify-center w-full md:w-auto px-5 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-medium text-sm rounded-xl transition-all cursor-pointer active:scale-95"
          >
            <RefreshCw className="w-4 h-4 text-slate-300" />
            Refresh Data
          </button>
        </div>

        {/* Dynamic States */}
        {loading ? (
          /* Show exactly 6 SkeletonCard components */
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3, 4, 5, 6].map((i) => (
              <SkeletonCard key={i} />
            ))}
          </div>
        ) : error ? (
          <ErrorState message={error} onRetry={() => refetch()} />
        ) : teams.length === 0 ? (
          /* Empty state for absolutely no teams registered in the backend */
          <EmptyState
            icon={<Users size={32} />}
            title="Belum ada tim terdaftar"
            description="Silakan daftarkan tim kamu sekarang melalui menu pendaftaran untuk menjadi bagian dari turnamen megah ini."
          />
        ) : filteredTeams.length === 0 ? (
          /* Empty state for search results */
          <EmptyState
            icon={<Users size={32} />}
            title="Tim Tidak Ditemukan"
            description={`Tidak ada tim yang cocok dengan kata kunci "${searchQuery}". Silakan coba kata kunci lain.`}
          />
        ) : (
          /* Teams Grid: 3 columns on desktop, 2 on tablet, 1 on mobile */
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredTeams.map((team, index) => (
              <TeamCard key={team.id} team={team} index={index} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
