import React from 'react';
import { motion } from 'motion/react';
import { GitMerge, HelpCircle, Trophy, RefreshCw, Award } from 'lucide-react';
import useFetch from '../hooks/useFetch';
import { SkeletonCard } from '../components/LoadingSkeleton';
import EmptyState from '../components/EmptyState';
import ErrorState from '../components/ErrorState';

interface Team {
  id: number;
  name: string;
}

interface Match {
  id: number;
  round: string;
  status: 'scheduled' | 'ongoing' | 'done' | 'cancelled';
  match_date: string;
  venue: string;
  team1: Team;
  team2: Team;
  score?: {
    team1: number;
    team2: number;
  };
  winner_id: number | null;
}

interface BracketRound {
  round: string;
  matches: Match[];
}

// Sub-component for individual bracket match card with exact status tags & connector lines
function MatchBracketCard({ 
  match, 
  roundIdx, 
  totalRounds, 
  matchIdx 
}: { 
  match: Match; 
  roundIdx: number; 
  totalRounds: number; 
  matchIdx?: number; 
}) {
  const isBye = match.team1 && match.team2 && match.team1.id === match.team2.id;
  const isWinnerTeam1 = match.status === 'done' && match.winner_id === match.team1.id;
  const isWinnerTeam2 = match.status === 'done' && match.winner_id === match.team2.id;

  const renderStatusBadge = (status: 'scheduled' | 'ongoing' | 'done' | 'cancelled') => {
    switch (status) {
      case 'ongoing':
        return (
          <span className="inline-flex items-center gap-1 text-[8px] font-mono font-black px-1.5 py-0.5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-full animate-pulse">
            <span className="w-1 h-1 bg-red-500 rounded-full animate-ping" />
            LIVE
          </span>
        );
      case 'done':
        return (
          <span className="inline-flex items-center gap-1 text-[8px] font-mono font-bold px-1.5 py-0.5 bg-[#E4FD97]/10 border border-[#E4FD97]/20 text-[#E4FD97] rounded-full">
            SELESAI
          </span>
        );
      case 'cancelled':
        return (
          <span className="inline-flex items-center gap-1 text-[8px] font-mono font-bold px-1.5 py-0.5 bg-slate-500/10 border border-slate-500/20 text-slate-400 rounded-full">
            BATAL
          </span>
        );
      case 'scheduled':
      default:
        return (
          <span className="inline-flex items-center gap-1 text-[8px] font-mono font-bold px-1.5 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-full">
            TERJADWAL
          </span>
        );
    }
  };

  return (
    <motion.div
      whileHover={{ y: -2, scale: 1.01 }}
      className={`glass-card p-4 rounded-xl border relative shadow-md transition-all duration-300 group ${
        match.status === 'ongoing' 
          ? 'border-red-500/30 bg-gradient-to-br from-slate-950 via-red-950/5 to-slate-950 shadow-[0_4px_15px_rgba(239,68,68,0.15)]' 
          : 'border-white/5 hover:border-white/15'
      }`}
    >
      {/* Connector line - Right (goes to center of gap) */}
      {totalRounds > 0 && roundIdx < totalRounds - 1 && (
        <div className="hidden lg:block absolute right-[-24px] top-1/2 -translate-y-1/2 w-[24px] h-[2px] bg-white/10 group-hover:bg-[#E4FD97]/20 transition-colors pointer-events-none" />
      )}
      {/* Connector line - Left */}
      {totalRounds > 0 && roundIdx > 0 && (
        <div className="hidden lg:block absolute left-[-24px] top-1/2 -translate-y-1/2 w-[24px] h-[2px] bg-white/10 group-hover:bg-[#E4FD97]/20 transition-colors pointer-events-none" />
      )}

      {/* Card Header */}
      <div className="flex items-center justify-between mb-3 pb-2 border-b border-white/5">
        <span className="text-[9px] font-mono text-slate-500 font-bold uppercase tracking-wider">
          MATCH ID: #{match.id}
        </span>
        {renderStatusBadge(match.status)}
      </div>

      {isBye ? (
        /* SPECIAL BYE HANDLING COMPLIANT WITH USER SPECIFICATION */
        <div className="py-3 px-3 bg-white/[0.02] border border-dashed border-white/5 rounded-xl text-center">
          <p className="text-xs text-slate-400 italic font-medium leading-relaxed">
            <span className="font-bold text-slate-300 not-italic block mb-0.5">{match.team1?.name || 'TBA'}</span>
            — BYE <span className="text-slate-500 font-normal">(otomatis lolos)</span>
          </p>
        </div>
      ) : (
        /* Normal Matchup Representation */
        <div className="space-y-3">
          
          {/* Team 1 Row */}
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2.5 truncate pr-2">
              <div className={`w-5 h-5 rounded-full flex items-center justify-center font-display font-bold text-[9px] uppercase shrink-0 ${
                isWinnerTeam1 
                  ? 'bg-[#E4FD97]/20 text-[#E4FD97]' 
                  : 'bg-white/5 text-slate-400 border border-white/5'
              }`}>
                {(match.team1?.name || '?').substring(0, 1)}
              </div>
              <span className={`text-xs truncate ${
                isWinnerTeam1 
                  ? 'text-[#E4FD97] font-extrabold text-glow-accent' 
                  : match.status === 'done' 
                    ? 'text-slate-500 font-medium' 
                    : 'text-white font-semibold'
              }`}>
                {match.team1?.name || 'TBA'}
              </span>
            </div>

            {/* Score 1 */}
            {(match.status === 'done' || match.status === 'ongoing') ? (
              <span className={`font-mono text-xs font-black px-1.5 py-0.5 rounded ${
                isWinnerTeam1 ? 'bg-[#E4FD97]/15 text-[#E4FD97]' : 'text-slate-400 bg-white/5'
              }`}>
                {match.score?.team1 ?? 0}
              </span>
            ) : (
              <span className="text-[10px] font-mono text-slate-600">-</span>
            )}
          </div>

          {/* Divider */}
          <div className="h-[1px] bg-white/[0.04]" />

          {/* Team 2 Row */}
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2.5 truncate pr-2">
              <div className={`w-5 h-5 rounded-full flex items-center justify-center font-display font-bold text-[9px] uppercase shrink-0 ${
                isWinnerTeam2 
                  ? 'bg-[#E4FD97]/20 text-[#E4FD97]' 
                  : 'bg-white/5 text-slate-400 border border-white/5'
              }`}>
                {(match.team2?.name || '?').substring(0, 1)}
              </div>
              <span className={`text-xs truncate ${
                isWinnerTeam2 
                  ? 'text-[#E4FD97] font-extrabold text-glow-accent' 
                  : match.status === 'done' 
                    ? 'text-slate-500 font-medium' 
                    : 'text-white font-semibold'
              }`}>
                {match.team2?.name || 'TBA'}
              </span>
            </div>

            {/* Score 2 */}
            {(match.status === 'done' || match.status === 'ongoing') ? (
              <span className={`font-mono text-xs font-black px-1.5 py-0.5 rounded ${
                isWinnerTeam2 ? 'bg-[#E4FD97]/15 text-[#E4FD97]' : 'text-slate-400 bg-white/5'
              }`}>
                {match.score?.team2 ?? 0}
              </span>
            ) : (
              <span className="text-[10px] font-mono text-slate-600">-</span>
            )}
          </div>

        </div>
      )}

      {/* Winner decoration label for finished matches */}
      {match.status === 'done' && !isBye && match.winner_id && (
        <div className="mt-3 pt-2 border-t border-white/[0.04] flex items-center justify-between text-[10px]">
          <span className="text-slate-500 font-medium">Lolos:</span>
          <span className="font-extrabold text-[#E4FD97] truncate max-w-[150px]">
            {match.winner_id === match.team1?.id ? match.team1?.name : match.team2?.name}
          </span>
        </div>
      )}
    </motion.div>
  );
}

export default function Bracket() {
  const { data: response, loading, error, refetch } = useFetch<{ data: BracketRound[] }>('/bracket');

  // Extracts bracket groupings, handles both response formats
  const bracketRounds: BracketRound[] = response?.data || (Array.isArray(response) ? (response as any) : []);

  return (
    <div id="bracket-page" className="relative min-h-screen bg-transparent pt-28 pb-20 px-4 sm:px-6 lg:px-8">
      {/* Decorative Background Ornaments */}
      <div className="absolute top-20 left-1/4 w-80 h-80 bg-[#E4FD97]/5 rounded-full blur-[100px] pointer-events-none" />
      <div className="absolute bottom-10 right-10 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px] pointer-events-none" />

      <div className="max-w-7xl mx-auto space-y-10 relative z-10">
        
        {/* Header */}
        <div className="text-center space-y-3 max-w-2xl mx-auto">
          <span className="text-xs font-mono font-bold tracking-wider text-[#E4FD97] uppercase flex items-center justify-center gap-1.5">
            <GitMerge className="w-4 h-4 text-[#E4FD97]" />
            Bagan Sistem Gugur
          </span>
          <h1 className="font-display text-3xl sm:text-4xl font-extrabold uppercase text-white tracking-tight">
            BAGAN TURNAMEN (BRACKET)
          </h1>
          <p className="text-slate-400 text-sm font-sans leading-relaxed">
            Bagan kompetisi sistem gugur dari babak penyisihan hingga babak Final. Tim pemenang di setiap cabang akan maju ke babak berikutnya.
          </p>
        </div>

        {/* Action Controls */}
        {!loading && !error && bracketRounds.length > 0 && (
          <div className="flex justify-end">
            <button
              onClick={() => refetch()}
              className="flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-medium text-xs rounded-xl transition-all cursor-pointer active:scale-95"
            >
              <RefreshCw className="w-3.5 h-3.5 text-slate-300" />
              <span>Refresh Bagan</span>
            </button>
          </div>
        )}

        {/* Dynamic States */}
        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {[1, 2, 3].map((r) => (
              <div key={r} className="space-y-6">
                <div className="h-6 bg-white/5 rounded w-32 animate-pulse mb-4" />
                <SkeletonCard />
                <SkeletonCard />
              </div>
            ))}
          </div>
        ) : error ? (
          <ErrorState message={error} onRetry={() => refetch()} />
        ) : bracketRounds.length === 0 ? (
          <EmptyState
            icon={<GitMerge size={32} />}
            title="Belum ada bagan eliminasi"
            description="Knockout/bagan bracket akan dibuat otomatis setelah seluruh pertandingan penyisihan selesai dan memasuki fase sistem gugur."
          />
        ) : (
          <div>
            
            {/* MOBILE LAYOUT (Stacked vertical blocks, visible on mobile and tablet) */}
            <div className="block lg:hidden space-y-10">
              {bracketRounds.map((roundGroup) => (
                <div key={`mobile-${roundGroup.round}`} className="space-y-4">
                  <div className="flex items-center justify-between border-b border-white/10 pb-2">
                    <h3 className="font-display font-extrabold text-sm text-[#E4FD97] uppercase tracking-wider flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-[#E4FD97]" />
                      {roundGroup.round}
                    </h3>
                    <span className="text-[10px] font-mono font-bold bg-white/5 text-slate-400 py-0.5 px-2.5 rounded-lg border border-white/5">
                      {roundGroup.matches.length} Pertandingan
                    </span>
                  </div>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {roundGroup.matches.map((match) => (
                      <MatchBracketCard 
                        key={`mobile-match-${match.id}`} 
                        match={match} 
                        roundIdx={0} 
                        totalRounds={0} 
                      />
                    ))}
                  </div>
                </div>
              ))}
            </div>

            {/* DESKTOP LAYOUT (Horizontal columns with connector lines, visible on large screens) */}
            <div className="hidden lg:flex gap-12 overflow-x-auto pb-8 pt-4 items-stretch min-h-[620px] scrollbar-thin scrollbar-thumb-white/5 scrollbar-track-transparent">
              {bracketRounds.map((roundGroup, roundIdx) => (
                <div
                  key={`desktop-${roundGroup.round}`}
                  className="flex-1 min-w-[280px] max-w-[340px] flex flex-col relative border-r last:border-r-0 border-white/[0.03] pr-6 last:pr-0"
                >
                  {/* Column Header */}
                  <div className="text-center mb-6 border-b border-white/5 pb-3">
                    <h3 className="font-display font-extrabold text-xs text-[#E4FD97] uppercase tracking-widest flex items-center justify-center gap-2">
                      <span className="w-1.5 h-1.5 rounded-full bg-[#E4FD97]" />
                      {roundGroup.round}
                    </h3>
                    <p className="text-[10px] font-mono text-slate-500 mt-1 uppercase font-bold">
                      {roundGroup.matches.length} Matchups
                    </p>
                  </div>

                  {/* Vertically spaced Matches Container */}
                  <div className="flex flex-col justify-around flex-grow relative py-6 min-h-[480px]">
                    {roundGroup.matches.map((match, idx) => (
                      <div key={`desktop-match-${match.id}`} className="relative py-4">
                        <MatchBracketCard 
                          match={match} 
                          roundIdx={roundIdx} 
                          totalRounds={bracketRounds.length} 
                          matchIdx={idx}
                        />
                      </div>
                    ))}
                  </div>
                </div>
              ))}
            </div>

          </div>
        )}
      </div>
    </div>
  );
}
