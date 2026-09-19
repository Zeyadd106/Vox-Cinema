import { Link } from 'react-router-dom';

const OFFERS = [
  {
    title: 'Ladies Morning Offer',
    badge: 'Every Tue Morning',
    body: 'Ladies enjoy discounted Standard tickets for all morning showtimes before 1pm every Tuesday, plus 20% off popcorn combos. Grab your friends and make it a ritual.',
    cta: 'See Morning Showtimes',
  },
  {
    title: 'Student Discount',
    badge: 'With Valid ID',
    body: 'Students get 15% off Standard tickets Sunday to Wednesday with a valid student ID, at the box office or online. Big screen, small budget.',
    cta: 'Browse Movies',
  },
  {
    title: 'Family Bundle',
    badge: '2 Adults + 2 Kids',
    body: 'Two adults and two kids, any KIDS screening, with a large popcorn and four drinks included — one simple price for the whole family outing.',
    cta: 'See Kids Screenings',
  },
];

export default function Offers() {
  return (
    <div className="mx-auto max-w-6xl px-[6%] py-12">
      <p className="text-center text-sm uppercase tracking-widest text-[#888]">Offers</p>
      <h1 className="mb-2 text-center text-3xl font-bold uppercase tracking-widest text-vox">Deals Worth Leaving Home For</h1>
      <p className="mx-auto mb-10 max-w-2xl text-center text-[#aaa]">New offers land every month. Show the relevant ID at the box office or apply codes automatically at checkout.</p>
      <div className="grid gap-6 md:grid-cols-3">
        {OFFERS.map((o) => (
          <article key={o.title} className="flex flex-col overflow-hidden rounded-lg border border-[#333] bg-[#1a1a1a] transition hover:-translate-y-1 hover:border-vox">
            <div className="bg-gradient-to-br from-vox-dark via-[#3a0a14] to-black px-5 py-6">
              <span className="rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white">{o.badge}</span>
              <h2 className="mt-3 text-2xl font-bold">{o.title}</h2>
            </div>
            <p className="flex-1 p-5 text-sm leading-relaxed text-[#bbb]">{o.body}</p>
            <div className="p-5 pt-0">
              <Link to="/movies" className="inline-block rounded-md border-2 border-vox px-5 py-2 text-sm font-semibold transition hover:bg-vox">{o.cta}</Link>
            </div>
          </article>
        ))}
      </div>
    </div>
  );
}
