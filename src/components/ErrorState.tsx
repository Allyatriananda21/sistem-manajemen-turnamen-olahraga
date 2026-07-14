import React from 'react';
import { AlertTriangle, RefreshCw } from 'lucide-react';

interface ErrorStateProps {
  message: string;
  onRetry?: () => void;
}

export default function ErrorState({ message, onRetry }: ErrorStateProps) {
  return (
    <div className="glass-card p-10 rounded-3xl border border-red-500/10 flex flex-col items-center justify-center text-center max-w-md mx-auto my-8 relative overflow-hidden">
      <div className="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 mb-6 border border-red-500/20">
        <AlertTriangle size={32} />
      </div>
      <h3 className="text-lg font-bold mb-2 tracking-tight text-white">Terjadi Kesalahan</h3>
      <p className="text-slate-400 text-sm leading-relaxed mb-6">{message}</p>
      {onRetry && (
        <button
          onClick={onRetry}
          className="inline-flex items-center gap-2 px-6 py-2.5 bg-red-500/20 hover:bg-red-500/30 text-red-200 border border-red-500/30 rounded-full text-sm font-semibold transition-all duration-300 active:scale-95"
        >
          <RefreshCw size={16} />
          Coba Lagi
        </button>
      )}
    </div>
  );
}
