import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Trophy, RefreshCw, ListOrdered, Star, ArrowUpRight, Shield, Layers, LayoutGrid, Sliders, Medal } from 'lucide-react';
import useFetch from '../hooks/useFetch';
import EmptyState from '../components/EmptyState';
import ErrorState from '../components/ErrorState';

interface StandingItem {
  team_id: number;
  team_name: string;
  played: number;
  win: number;
  draw: number;
  lose: number;
  points: number;
  goal_diff: number;
}

// Custom 8-row Table Skeleton Loader
function SkeletonTableEightRows() {
  return (
    <div className="glass-card p-6 rounded-2xl border border-white/5 space-y-4 animate-pulse">
      {/* Table Header Skeleton */}
      <div className="grid grid-cols-8 gap-4 border-b border-white/5 pb-4">
        <div className="h-5 bg-white/5 rounded col-span-1" />
        <div className="h-5 bg-white/5 rounded col-span-3" />
        {[1, 2, 3, 4].map((i) => (
          <div key={i} className="h-5 bg-white/5 rounded col-span-1" />
        ))}
      </div>
      {/* 8 Rows of Skeletons */}
      {[1, 2, 3, 4, 5, 6, 7, 8].map((idx) => (
        <div key={idx} className="grid grid-cols-8 gap-4 py-3 border-b border-white/[0.02]">
          <div className="h-5 bg-white/5 rounded col-span-1" />
          <div className="h-5 bg-white/5 rounded col-span-3" />
          <div className="h-5 bg-white/5 rounded col-span-1" />
          <div className="h-5 bg-white/5 rounded col-span-1" />
          <div className="h-5 bg-white/5 rounded col-span-1" />
          <div className="h-5 bg-white/5 rounded col-span-1" />
        </div>
      ))}
    </div>
  );
}

export default function Standings() {
  const [viewMode, setViewMode] = useState<'compact' | 'full'>('compact');
  const { data: response, loading, error, refetch } = useFetch<{ data: StandingItem[] }>('/standings');

  // Extract standings array safely
  const standings: StandingItem[] = response?.data || (Array.isArray(response) ? (response as any) : []);

  // Format Goal Difference cleanly
  const formatGoalDiff = (gd: number) => {
    if (gd > 0) return `+${gd}`;
    if (gd < 0) return `${gd}`;
    return '0';
  };

  return (
    <div id="standings-page" className="relative min-h-screen bg-transparent pt-28 pb-20 px-4 sm:px-6 lg:px-8">
      {/* Dynamic Glowing Accents */}
      <div className="absolute top-10 right-1/4 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none" />
      <div className="absolute bottom-10 left-10 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px] pointer-events-none" />

      <div className="max-w-6xl mx-auto space-y-8 relative z-10">
        
        {/* Header */}
        <div className="text-center space-y-3 max-w-2xl mx-auto">
          <span className="text-xs font-mono font-bold tracking-wider text-[#E4FD97] uppercase flex items-center justify-center gap-1.5">
            <Trophy className="w-4 h-4 text-[#E4FD97]" />
            TABEL KLASEMEN LIGA
          </span>
          <h1 className="font-display text-3xl sm:text-4xl font-extrabold uppercase text-white tracking-tight">
            PERINGKAT & AKUMULASI POIN
          </h1>
          <p className="text-slate-400 text-sm font-sans leading-relaxed">
            Peringkat dihitung otomatis berdasarkan akumulasi kemenangan, hasil seri, dan selisih gol. Pantau posisi tim jagoanmu setiap saat.
          </p>
        </div>

        {/* View Mode Swapper & Refresh Controls */}
        {!loading && !error && standings.length > 0 && (
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white/[0.02] border border-white/5 rounded-2xl p-4">
            
            {/* View Mode selector for Mobile/Tablet layout optimization */}
            <div className="flex items-center gap-2 bg-slate-950/80 p-1 rounded-xl border border-white/5 w-full sm:w-auto">
              <button
                onClick={() => setViewMode('compact')}
                className={`flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold tracking-wide transition-all cursor-pointer ${
                  viewMode === 'compact'
                    ? 'bg-[#E4FD97] text-[#2D3E2C] font-bold shadow-md'
                    : 'text-slate-400 hover:text-white'
                }`}
              >
                <Layers className="w-3.5 h-3.5" />
                <span>Ringkas</span>
              </button>
              <button
                onClick={() => setViewMode('full')}
                className={`flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold tracking-wide transition-all cursor-pointer ${
                  viewMode === 'full'
                    ? 'bg-[#E4FD97] text-[#2D3E2C] font-bold shadow-md'
                    : 'text-slate-400 hover:text-white'
                }`}
              >
                <Sliders className="w-3.5 h-3.5" />
                <span>Detail (Full)</span>
              </button>
            </div>

            {/* Manual Refresh Action */}
            <button
              onClick={() => refetch()}
              className="flex items-center gap-2 justify-center w-full sm:w-auto px-5 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-medium text-xs rounded-xl transition-all cursor-pointer active:scale-95"
            >
              <RefreshCw className="w-3.5 h-3.5 text-slate-300" />
              <span>Perbarui Klasemen</span>
            </button>
          </div>
        )}

        {/* Dynamic States rendering */}
        {loading ? (
          <SkeletonTableEightRows />
        ) : error ? (
          <ErrorState message={error} onRetry={() => refetch()} />
        ) : standings.length === 0 ? (
          /* Empty state matching the exact string specified */
          <EmptyState
            icon={<ListOrdered size={32} />}
            title="Klasemen belum tersedia — menunggu pertandingan selesai"
            description="Tabel peringkat akan diperbarui otomatis begitu laga pertama diselesaikan dan skor diinput oleh panitia."
          />
        ) : (
          <div className="space-y-6">
            
            {/* Desktop and Tablet Full scrollable Table layout */}
            <div className={`glass-card rounded-2xl border border-white/5 overflow-hidden shadow-2xl ${viewMode === 'compact' ? 'hidden md:block' : 'block'}`}>
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse min-w-[750px]">
                  <thead>
                    <tr className="border-b border-white/5 bg-white/[0.02] text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">
                      <th className="py-4 px-6 text-center w-16"># Pos</th>
                      <th className="py-4 px-4">Tim</th>
                      <th className="py-4 px-4 text-center w-24">M (Main)</th>
                      <th className="py-4 px-4 text-center w-24">M (Menang)</th>
                      <th className="py-4 px-4 text-center w-24">S (Seri)</th>
                      <th className="py-4 px-4 text-center w-24">K (Kalah)</th>
                      <th className="py-4 px-4 text-center w-32">SG (Selisih Gol)</th>
                      <th className="py-4 px-6 text-center text-[#E4FD97] bg-[#E4FD97]/10 font-black w-28">Poin</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-white/5 text-sm font-sans">
                    {standings.map((team, index) => {
                      const rank = index + 1;
                      
                      // Precise styling for Podium Gold, Silver, and Bronze
                      let rowBg = 'hover:bg-white/[0.02] even:bg-white/[0.01]';
                      let rankBadgeClass = '';
                      let rankTextClass = 'text-slate-300';
                      let medalIcon: React.ReactNode = null;

                      if (rank === 1) {
                        rowBg = 'bg-gradient-to-r from-yellow-500/10 via-yellow-500/2 to-transparent hover:from-yellow-500/15 transition-all border-l-2 border-yellow-500';
                        rankBadgeClass = 'bg-gradient-to-r from-yellow-400 to-amber-500 text-[#0F172A] font-black shadow-[0_0_12px_rgba(234,179,8,0.4)]';
                        rankTextClass = 'text-yellow-400 font-extrabold';
                        medalIcon = <Medal className="w-4 h-4 text-yellow-400" />;
                      } else if (rank === 2) {
                        rowBg = 'bg-gradient-to-r from-slate-300/10 via-slate-300/2 to-transparent hover:from-slate-300/15 transition-all border-l-2 border-slate-300';
                        rankBadgeClass = 'bg-gradient-to-r from-slate-300 to-slate-400 text-[#0F172A] font-black shadow-[0_0_12px_rgba(203,213,225,0.2)]';
                        rankTextClass = 'text-slate-300 font-bold';
                        medalIcon = <Medal className="w-4 h-4 text-slate-300" />;
                      } else if (rank === 3) {
                        rowBg = 'bg-gradient-to-r from-amber-700/10 via-amber-700/2 to-transparent hover:from-amber-700/15 transition-all border-l-2 border-amber-600';
                        rankBadgeClass = 'bg-gradient-to-r from-amber-600 to-amber-700 text-white font-black shadow-[0_0_12px_rgba(180,83,9,0.2)]';
                        rankTextClass = 'text-amber-500 font-bold';
                        medalIcon = <Medal className="w-4 h-4 text-amber-500" />;
                      }

                      return (
                        <tr key={team.team_id} className={`transition-all duration-200 ${rowBg}`}>
                          {/* RANK */}
                          <td className="py-4 px-6 text-center font-mono">
                            {rankBadgeClass ? (
                              <span className={`inline-flex items-center justify-center w-6 h-6 rounded-md text-xs relative ${rankBadgeClass}`}>
                                {rank}
                              </span>
                            ) : (
                              <span className="text-slate-500 text-xs font-bold">{rank}</span>
                            )}
                          </td>

                          {/* TEAM IDENTITY */}
                          <td className="py-4 px-4 font-display font-semibold text-white">
                            <div className="flex items-center gap-3">
                              <div className="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-xs text-slate-300 font-bold uppercase shrink-0">
                                {team.team_name.substring(0, 2)}
                              </div>
                              <div className="flex flex-col">
                                <span className="truncate max-w-[220px] text-sm font-bold text-white" title={team.team_name}>
                                  {team.team_name}
                                </span>
                              </div>
                              {medalIcon && (
                                <span className="text-sm shrink-0">{medalIcon}</span>
                              )}
                            </div>
                          </td>

                          {/* MATCHES PLAYED */}
                          <td className="py-4 px-4 text-center font-mono text-slate-300 font-medium">
                            {team.played}
                          </td>

                          {/* WIN */}
                          <td className="py-4 px-4 text-center font-mono text-emerald-400 font-medium">
                            {team.win}
                          </td>

                          {/* DRAW */}
                          <td className="py-4 px-4 text-center font-mono text-slate-400">
                            {team.draw}
                          </td>

                          {/* LOSE */}
                          <td className="py-4 px-4 text-center font-mono text-red-400">
                            {team.lose}
                          </td>

                          {/* GOAL DIFFERENCE */}
                          <td className="py-4 px-4 text-center font-mono">
                            {team.goal_diff > 0 ? (
                              <span className="text-emerald-400 font-bold">+{team.goal_diff}</span>
                            ) : team.goal_diff < 0 ? (
                              <span className="text-red-400 font-medium">{team.goal_diff}</span>
                            ) : (
                              <span className="text-slate-500">0</span>
                            )}
                          </td>

                          {/* TOTAL ACCUMULATED POINTS */}
                          <td className={`py-4 px-6 text-center font-mono text-base ${rankTextClass} bg-[#E4FD97]/5 border-r border-white/5`}>
                            {team.points}
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            </div>

            {/* Mobile / Tablet Optimized Compact View (Showing Pos, Name, & Points only) */}
            <div className={`space-y-3 ${viewMode === 'compact' ? 'block md:hidden' : 'hidden'}`}>
              <div className="flex items-center justify-between text-xs font-mono font-bold text-slate-400 px-4 uppercase tracking-wider">
                <span># POS • TIM</span>
                <span>POIN</span>
              </div>
              <div className="space-y-2.5">
                {standings.map((team, index) => {
                  const rank = index + 1;
                  
                  // Mobile specific premium card styles
                  let cardBorder = 'border-white/5 bg-[#2D3E2C]/50';
                  let rankColor = 'text-slate-500';
                  let rankBg = 'bg-white/5';
                  let pointsColor = 'text-[#E4FD97]';
                  let medal: React.ReactNode = null;

                  if (rank === 1) {
                    cardBorder = 'border-yellow-500/30 bg-gradient-to-r from-yellow-500/10 via-slate-900/40 to-slate-900/20';
                    rankBg = 'bg-gradient-to-r from-yellow-400 to-amber-500 text-dark-bg';
                    rankColor = 'text-[#0F172A]';
                    pointsColor = 'text-yellow-400 font-black';
                    medal = <Medal className="w-4 h-4 text-yellow-400" />;
                  } else if (rank === 2) {
                    cardBorder = 'border-slate-400/20 bg-gradient-to-r from-slate-300/10 via-slate-900/40 to-slate-900/20';
                    rankBg = 'bg-gradient-to-r from-slate-300 to-slate-400 text-dark-bg';
                    rankColor = 'text-[#0F172A]';
                    pointsColor = 'text-slate-300 font-bold';
                    medal = <Medal className="w-4 h-4 text-slate-300" />;
                  } else if (rank === 3) {
                    cardBorder = 'border-amber-600/20 bg-gradient-to-r from-amber-700/10 via-slate-900/40 to-slate-900/20';
                    rankBg = 'bg-gradient-to-r from-amber-600 to-amber-700 text-white';
                    rankColor = 'text-white';
                    pointsColor = 'text-amber-500 font-bold';
                    medal = <Medal className="w-4 h-4 text-amber-500" />;
                  }

                  return (
                    <motion.div
                      initial={{ opacity: 0, y: 10 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ duration: 0.3, delay: Math.min(index * 0.04, 0.3) }}
                      key={`compact-${team.team_id}`}
                      className={`glass-card p-4 rounded-xl border flex items-center justify-between shadow ${cardBorder}`}
                    >
                      <div className="flex items-center gap-3 min-w-0">
                        {/* Compact Rank Bubble */}
                        <div className={`w-6 h-6 rounded-md font-mono text-xs font-bold flex items-center justify-center shrink-0 ${rankBg} ${rankColor}`}>
                          {rank}
                        </div>
                        <div className="flex items-center gap-2 min-w-0">
                          <span className="font-display font-bold text-white text-sm truncate" title={team.team_name}>
                            {team.team_name}
                          </span>
                          {medal && <span className="text-xs shrink-0">{medal}</span>}
                        </div>
                      </div>
                      
                      {/* Compact side display: Points and GD */}
                      <div className="flex items-center gap-4 shrink-0 font-mono">
                        <div className="text-right">
                          <div className="text-slate-400 text-[10px]">SG: {formatGoalDiff(team.goal_diff)}</div>
                          <div className="text-slate-400 text-[10px] sm:hidden">M: {team.played}</div>
                        </div>
                        <div className={`text-base font-bold px-3 py-1 bg-white/5 border border-white/5 rounded-lg ${pointsColor}`}>
                          {team.points}
                        </div>
                      </div>
                    </motion.div>
                  );
                })}
              </div>
            </div>

          </div>
        )}
      </div>
    </div>
  );
}
