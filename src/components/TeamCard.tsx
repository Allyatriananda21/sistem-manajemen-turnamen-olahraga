import React from 'react';
import { motion } from 'motion/react';
import { ShieldCheck, ShieldAlert, Award } from 'lucide-react';
import resolveStorageUrl from '../utils/resolveStorageUrl';

export interface Team {
  id: number;
  name: string;
  sport_type: string;
  logo: string | null;
  payment_status: 'paid' | 'unpaid';
}

interface TeamCardProps {
  team: Team;
  index: number;
}

export default function TeamCard({ team, index }: TeamCardProps) {
  // Resolve logo URL through the Vite proxy (strips the Laravel origin so the
  // request goes via /storage/... instead of http://127.0.0.1:8000/storage/...)
  const logoSrc = resolveStorageUrl(team.logo);

  // Fallback avatar generated from team name when logo is absent or fails to load
  const fallbackAvatar = `https://api.dicebear.com/7.x/identicon/svg?seed=${encodeURIComponent(team.name)}`;

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.4, delay: Math.min(index * 0.05, 0.4) }}
      whileHover={{ y: -6, scale: 1.02 }}
      className="glass-card p-6 rounded-2xl border border-white/5 text-center flex flex-col items-center justify-between min-h-[260px] relative overflow-hidden shadow-lg transition-all duration-300 hover:border-[#E4FD97]/30 hover:shadow-[0_8px_24px_rgba(228,253,151,0.15)] group"
    >
      {/* Top accent visual */}
      <div className="absolute top-0 inset-x-0 h-[3px] bg-gradient-to-r from-transparent via-[#E4FD97]/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />

      {/* Logo container with fallback mechanism */}
      <div className="my-2 relative">
        {logoSrc ? (
          <img
            src={logoSrc}
            alt={`Logo ${team.name}`}
            className="w-20 h-20 rounded-full object-cover border-2 border-white/10 bg-slate-800/50 shadow-lg group-hover:border-[#E4FD97]/30 transition-colors"
            onError={(e) => {
              e.currentTarget.src = fallbackAvatar;
            }}
          />
        ) : (
          <div className="w-20 h-20 rounded-full bg-gradient-to-br from-[#3e563d] to-[#1e2a1e] border border-white/10 flex items-center justify-center text-white text-3xl font-display font-extrabold shadow-lg uppercase group-hover:border-[#E4FD97]/30 transition-colors">
            {team.name.substring(0, 2)}
          </div>
        )}
        <div className="absolute -bottom-1 -right-1 p-1 bg-[#2D3E2C] border border-white/10 rounded-full">
          <Award className="w-4 h-4 text-[#E4FD97]" />
        </div>
      </div>

      {/* Team Details */}
      <div className="space-y-1.5 mt-3 w-full">
        <h3 className="font-display font-bold text-base text-white tracking-tight truncate px-1" title={team.name}>
          {team.name}
        </h3>
        <p className="text-xs text-slate-400 font-medium tracking-wide uppercase">
          {team.sport_type || 'Cabang Olahraga'}
        </p>
      </div>

      {/* Payment Status Badge */}
      <div className="mt-4 w-full">
        {team.payment_status === 'paid' ? (
          <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-[#E4FD97]/10 border border-[#E4FD97]/20 text-[#E4FD97] text-xs font-semibold rounded-full">
            <ShieldCheck className="w-3.5 h-3.5 shrink-0" />
            <span>Lunas</span>
          </div>
        ) : (
          <div className="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold rounded-full">
            <ShieldAlert className="w-3.5 h-3.5 shrink-0 animate-pulse" />
            <span>Belum Lunas</span>
          </div>
        )}
      </div>
    </motion.div>
  );
}
