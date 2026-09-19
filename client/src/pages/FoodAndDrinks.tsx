import { Link } from 'react-router-dom';

const MENU = [
  { category: 'Popcorn', items: [
    { name: 'Classic Salted Popcorn', price: 95, desc: 'Freshly popped, served warm' },
    { name: 'Large Cheese Popcorn', price: 130, desc: 'Extra-cheesy sharing tub' },
    { name: 'Caramel Popcorn', price: 120, desc: 'Sweet golden crunch' },
  ]},
  { category: 'Snacks', items: [
    { name: 'Nachos with Cheese', price: 110, desc: 'Crispy nachos, molten cheese dip' },
    { name: 'Chicken Tenders (6 pc)', price: 150, desc: 'With smoky BBQ dip' },
    { name: 'Mozzarella Sticks', price: 135, desc: 'Golden, gooey, shareable' },
  ]},
  { category: 'Drinks', items: [
    { name: 'Soft Drink (Regular)', price: 55, desc: 'Pepsi, Mirinda, 7UP' },
    { name: 'Soft Drink (Large)', price: 70, desc: 'Jumbo cup, free refill' },
    { name: 'Bottled Water', price: 30, desc: 'Chilled 600ml' },
    { name: 'Fresh Orange Juice', price: 80, desc: 'Squeezed to order' },
  ]},
  { category: 'Sweet Treats', items: [
    { name: 'Chocolate Crepe', price: 90, desc: 'Nutella-filled, made fresh' },
    { name: 'Candy Mix Cup', price: 65, desc: 'Pick your favourites' },
    { name: 'Ice Cream Sundae', price: 85, desc: 'Vanilla, chocolate or mango' },
  ]},
];

export default function FoodAndDrinks() {
  return (
    <div className="mx-auto max-w-6xl px-[6%] py-12">
      <p className="text-center text-sm uppercase tracking-widest text-[#888]">Food & Drinks</p>
      <h1 className="mb-2 text-center text-3xl font-bold uppercase tracking-widest text-vox">Pre-Order Online</h1>
      <p className="mx-auto mb-10 max-w-2xl text-center text-[#aaa]">
        Skip the queue — order your favourite cinema treats and pick them up at the counter on your way in.
        Online pre-order at checkout is coming soon; the full menu is available at every location today.
      </p>
      {MENU.map((section) => (
        <div key={section.category} className="mb-10">
          <h2 className="mb-4 text-xl font-bold uppercase tracking-wider text-white">
            <span className="me-2 inline-block h-4 w-1 rounded bg-vox align-middle" />{section.category}
          </h2>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {section.items.map((item) => (
              <div key={item.name} className="rounded-lg border border-[#333] bg-[#1a1a1a] p-5 transition hover:border-vox">
                <div className="flex items-start justify-between gap-3">
                  <h3 className="font-semibold">{item.name}</h3>
                  <span className="whitespace-nowrap rounded-full bg-vox/15 px-3 py-0.5 text-sm font-bold text-vox-light">{item.price} EGP</span>
                </div>
                <p className="mt-1 text-sm text-[#999]">{item.desc}</p>
              </div>
            ))}
          </div>
        </div>
      ))}
      <div className="text-center">
        <Link to="/" className="inline-block rounded-md bg-vox px-8 py-3 font-semibold transition hover:bg-vox-dark">Browse Movies</Link>
      </div>
    </div>
  );
}
