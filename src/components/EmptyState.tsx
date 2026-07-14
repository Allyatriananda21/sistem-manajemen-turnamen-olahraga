import React from 'react';

interface EmptyStateProps {
  icon?: React.ReactNode;
  title: string;
  description: string;
}

export default function EmptyState({ icon, title, description }: EmptyStateProps) {
  return (
    <div className="glass-card p-12 rounded-3xl border border-white/5 flex flex-col items-center justify-center text-center max-w-md mx-auto my-8 relative overflow-hidden">
      {icon && (
        <div className="w-16 h-16 rounded-full bg-[#1E3A5F]/40 flex items-center justify-center text-[#22C55E] mb-6 border border-[#22C55E]/20 text-3xl">
          {icon}
        </div>
      )}
      <h3 className="text-xl font-bold mb-2 tracking-tight text-white">{title}</h3>
      <p className="text-slate-400 text-sm leading-relaxed">{description}</p>
    </div>
  );
}
