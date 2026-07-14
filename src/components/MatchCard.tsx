import React from 'react';
import { motion } from 'motion/react';
import { Trophy, Clock, MapPin, Flame } from 'lucide-react';
import formatDate from '../utils/formatDate';

export interface MatchTeam {
  id: number;
  name: string;
}

export interface Match {
  id: number;
  round: string;
  status: 'scheduled' | 'ongoing' | 'done' | 'cancelled';
  match_date: string;
  venue: string;
  team1: MatchTeam;
  team2: MatchTeam;
  score?: {
    team1: number;
    team2: number;
  };
  winner_id: number | null;
}

interface MatchCardProps {
  match: Match;
  index: number;
}

export default function MatchCard({ match, index }: MatchCardProps) {
  const scoreAvailable = match.status === 'ongoing' || match.status === 'done';
  const isWinner1 = match.status === 'done' && match.winner_id === match.team1.id;
  const isWinner2 = match.status === 'done' && match.winner_id === match.team2.id;
  const isDraw = match.status === 'done' && match.winner_id === null;

  // Render status badge with correct colors
  const renderStatusBadge = () => {
    switch (match.status) {
      case 'ongoing':
        return (
          <span className="inline-flex items-center gap-1 text-[10px] font-mono px-2.5 py-0.5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-full font-bold animate-pulse">
            <span className="w-1.5 h-1.5 bg-red-500 rounded-full animate-ping mr-0.5" />
            LIVE
          </span>
        );
      case 'done':
        return (
          <span className="inline-flex items-center gap-1 text-[10px] font-mono px-2.5 py-0.5 bg-[#E4FD97]/10 border border-[#E4FD97]/20 text-[#E4FD97] rounded-full font-bold">
            SELESAI
          </span>
        );
      case 'cancelled':
        return (
          <span className="inline-flex items-center gap-1 text-[10px] font-mono px-2.5 py-0.5 bg-slate-500/10 border border-slate-500/20 text-slate-400 rounded-full font-bold">
            BATAL
          </span>
        );
      case 'scheduled':
      default:
        return (
          <span className="inline-flex items-center gap-1 text-[10px] font-mono px-2.5 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-full font-bold">
            TERJADWAL
          </span>
        );
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.35, delay: Math.min(index * 0.04, 0.3) }}
      whileHover={{ y: -4, scale: 1.01 }}
      className={`glass-card p-6 rounded-2xl border transition-all duration-300 flex flex-col justify-between overflow-hidden relative group ${
        match.status === 'ongoing'
          ? 'border-red-500/30 shadow-[0_0_20px_rgba(239,68,68,0.15)] bg-gradient-to-br from-slate-950 via-red-950/5 to-slate-950'
          : 'border-white/5 hover:border-white/10 hover:shadow-[0_12px_24px_rgba(0,0,0,0.4)]'
      }`}
    >
      <div>
        {/* Header Details */}
        <div className="flex items-center justify-between mb-4 pb-3 border-b border-white/5">
          <span className="text-[10px] font-mono px-2.5 py-0.5 bg-white/5 border border-white/10 rounded text-slate-300 uppercase font-bold tracking-wider">
            {match.round}
          </span>
          {renderStatusBadge()}
        </div>

        {/* Competitors and score layout */}
        <div className="flex items-center justify-between my-5">
          {/* Team 1 */}
          <div className="w-[41%] text-center">
            <div className={`w-11 h-11 rounded-full flex items-center justify-center mx-auto mb-2 font-display font-bold text-sm border transition-colors ${
              isWinner1 
                ? 'bg-[#E4FD97]/10 border-[#E4FD97]/30 text-[#E4FD97]' 
                : 'bg-white/5 border-white/10 text-white'
            }`}>
              {match.team1.name.substring(0, 2).toUpperCase()}
            </div>
            <h4 className={`text-xs font-bold truncate transition-colors ${
              isWinner1 
                ? 'text-[#E4FD97] font-black' 
                : isWinner2 
                  ? 'text-slate-500 font-medium' 
                  : 'text-white'
            }`} title={match.team1.name}>
              {match.team1.name}
            </h4>
          </div>

          {/* VS / Score Divider */}
          <div className="text-center font-display shrink-0 px-2">
            {scoreAvailable ? (
              <div className="flex flex-col items-center">
                <div className={`text-xl font-mono font-black tracking-wide flex items-center gap-1.5 ${
                  match.status === 'ongoing' ? 'text-red-400 text-glow-red animate-pulse' : 'text-[#E4FD97]'
                }`}>
                  <span>{match.score?.team1 ?? 0}</span>
                  <span className="text-slate-600 font-normal text-sm">-</span>
                  <span>{match.score?.team2 ?? 0}</span>
                </div>
                {match.status === 'ongoing' && (
                  <span className="text-[8px] font-mono text-red-500 tracking-wider font-extrabold uppercase animate-pulse mt-1">Laga Live</span>
                )}
              </div>
            ) : (
              <span className="text-[10px] font-mono px-2.5 py-1 bg-white/5 border border-white/5 text-slate-400 rounded-lg uppercase font-bold">
                VS
              </span>
            )}
          </div>

          {/* Team 2 */}
          <div className="w-[41%] text-center">
            <div className={`w-11 h-11 rounded-full flex items-center justify-center mx-auto mb-2 font-display font-bold text-sm border transition-colors ${
              isWinner2 
                ? 'bg-[#E4FD97]/10 border-[#E4FD97]/30 text-[#E4FD97]' 
                : 'bg-white/5 border-white/10 text-white'
            }`}>
              {match.team2.name.substring(0, 2).toUpperCase()}
            </div>
            <h4 className={`text-xs font-bold truncate transition-colors ${
              isWinner2 
                ? 'text-[#E4FD97] font-black' 
                : isWinner1 
                  ? 'text-slate-500 font-medium' 
                  : 'text-white'
            }`} title={match.team2.name}>
              {match.team2.name}
            </h4>
          </div>
        </div>
      </div>

      {/* Winner badges / Status notes */}
      {match.status === 'done' && (
        <div className="mb-4 text-center">
          {isDraw ? (
            <span className="inline-flex items-center gap-1 text-[10px] font-display font-extrabold tracking-wider uppercase px-3 py-1 rounded-full bg-slate-500/10 text-slate-300 border border-slate-500/20">
              SERI
            </span>
          ) : (
            <span className="inline-flex items-center gap-1.5 text-[10px] font-display font-extrabold tracking-wider uppercase px-3 py-1 rounded-full bg-[#E4FD97]/15 text-[#E4FD97] border border-[#E4FD97]/20">
              <Trophy className="w-3 h-3 text-[#E4FD97]" />
              {isWinner1 ? match.team1.name : match.team2.name} MENANG
            </span>
          )}
        </div>
      )}

      {/* Location and Timing details */}
      <div className="mt-auto pt-4 border-t border-white/5 text-[11px] text-slate-400 space-y-1.5">
        <div className="flex items-center gap-2">
          <Clock className="w-3.5 h-3.5 text-[#E4FD97] shrink-0" />
          <span className="truncate">{formatDate(match.match_date)}</span>
        </div>
        <div className="flex items-center gap-2">
          <MapPin className="w-3.5 h-3.5 text-[#E4FD97] shrink-0" />
          <span className="truncate">{match.venue || 'TBA'}</span>
        </div>
      </div>
    </motion.div>
  );
}
