export type Lang = 'en' | 'ar';

const en = {
  home: 'Home',
  cinemas: 'Cinemas',
  movies: 'Movies',
  whatsOn: "What's On",
  comingSoon: 'Coming Soon',
  foodDrinks: 'Food & Drinks',
  preOrder: 'Pre-Order Online',
  waysToWatch: 'Ways to Watch',
  sensory: 'Sensory Friendly',
  offers: 'Offers',
  ladiesOffer: 'Ladies Morning Offer',
  login: 'Login',
  signUp: 'Sign-Up',
  logout: 'Logout',
  dashboard: 'Dashboard',
  myBookings: 'My Bookings',
  admin: 'Admin',
  search: 'Search',
  searchPlaceholder: 'Search movies...',
  egypt: 'Egypt',
  allCinemas: 'All Cinemas',
  selectCinema: 'Select Your Cinema',
  findTimes: 'Find Times and Book',
  arabic: 'عربي',
};

export type Dict = typeof en;

const ar: Dict = {
  home: 'الرئيسية',
  cinemas: 'السينمات',
  movies: 'أفلام',
  whatsOn: 'يعرض الآن',
  comingSoon: 'قريبًا',
  foodDrinks: 'مأكولات ومشروبات',
  preOrder: 'اطلب أونلاين',
  waysToWatch: 'طرق المشاهدة',
  sensory: 'عروض مناسبة حسيًا',
  offers: 'العروض',
  ladiesOffer: 'عرض صباح السيدات',
  login: 'تسجيل الدخول',
  signUp: 'إنشاء حساب',
  logout: 'تسجيل الخروج',
  dashboard: 'لوحة التحكم',
  myBookings: 'حجوزاتي',
  admin: 'الإدارة',
  search: 'ابحث',
  searchPlaceholder: 'ابحث عن الأفلام...',
  egypt: 'مصر',
  allCinemas: 'كل السينمات',
  selectCinema: 'اختر السينما',
  findTimes: 'اعرف المواعيد واحجز',
  arabic: 'EN',
};

export const dicts: Record<Lang, Dict> = { en, ar };
