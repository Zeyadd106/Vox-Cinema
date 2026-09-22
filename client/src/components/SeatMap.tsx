import { Seat } from '../types';
import { useLang } from '../context/LangContext';

export default function SeatMap({
  seats,
  selected,
  onToggle,
}: {
  seats: Seat[];
  selected: number[];
  onToggle: (id: number) => void;
}) {
  const rows = [...new Set(seats.map((s) => s.row))].sort();
  const { t } = useLang();
  return (
    <div className="rounded-lg border border-[#333] bg-[#111] p-6">
      <div className="mx-auto mb-6 max-w-md rounded-b-full border-2 border-t-0 border-vox/60 py-2 text-center text-sm uppercase tracking-[4px] text-vox-light">
        {t.seat.screen}
      </div>
      <div className="space-y-2">
        {rows.map((row) => (
          <div key={row} className="flex items-center justify-center gap-1.5">
            <span className="w-6 text-center text-xs text-[#888]">{row}</span>
            {seats.filter((s) => s.row === row).map((s) => {
              const isSel = selected.includes(s.id);
              const locked = !s.is_available && !isSel;
              return (
                <button
                  key={s.id}
                  disabled={locked}
                  onClick={() => onToggle(s.id)}
                  title={s.seat_number + (s.is_held && !isSel ? ' (held)' : '')}
                  className={`h-8 w-8 rounded-t-lg text-[11px] font-semibold transition ${
                    locked
                      ? 'cursor-not-allowed bg-[#333] text-[#666]'
                      : isSel
                        ? 'bg-amber-400 text-black'
                        : 'bg-[#2a2a2a] text-white hover:bg-vox'
                  }`}
                >
                  {s.number}
                </button>
              );
            })}
          </div>
        ))}
      </div>
      <div className="mt-6 flex justify-center gap-6 text-xs text-[#aaa]">
        <span className="flex items-center gap-1.5"><span className="inline-block h-4 w-4 rounded bg-[#2a2a2a]" /> {t.seat.available}</span>
        <span className="flex items-center gap-1.5"><span className="inline-block h-4 w-4 rounded bg-amber-400" /> {t.seat.selected}</span>
        <span className="flex items-center gap-1.5"><span className="inline-block h-4 w-4 rounded bg-[#333]" /> {t.seat.booked}</span>
      </div>
    </div>
  );
}
