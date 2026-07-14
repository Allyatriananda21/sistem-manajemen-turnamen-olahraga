import React from 'react';

export function SkeletonCard() {
  return (
    <div className="glass-card p-6 rounded-2xl border border-white/5 animate-pulse flex flex-col justify-between min-h-[250px] relative overflow-hidden">
      <div className="w-20 h-20 rounded-full bg-white/5 mx-auto mb-4" />
      <div className="space-y-2 flex flex-col items-center">
        <div className="w-3/4 h-5 bg-white/5 rounded" />
        <div className="w-1/2 h-4 bg-white/5 rounded" />
      </div>
      <div className="w-32 h-7 bg-white/5 rounded-full mx-auto mt-4" />
    </div>
  );
}

export function SkeletonTable() {
  return (
    <div className="glass-card p-6 rounded-2xl border border-white/5 space-y-4 animate-pulse">
      <div className="grid grid-cols-6 gap-4 border-b border-white/5 pb-4">
        {[1, 2, 3, 4, 5, 6].map((i) => (
          <div key={i} className="h-6 bg-white/5 rounded" />
        ))}
      </div>
      {[1, 2, 3, 4, 5].map((idx) => (
        <div key={idx} className="grid grid-cols-6 gap-4 py-3">
          <div className="h-5 bg-white/5 rounded col-span-2" />
          <div className="h-5 bg-white/5 rounded" />
          <div className="h-5 bg-white/5 rounded" />
          <div className="h-5 bg-white/5 rounded" />
          <div className="h-5 bg-white/5 rounded" />
        </div>
      ))}
    </div>
  );
}
