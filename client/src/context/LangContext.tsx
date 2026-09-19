import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { dicts, Lang, Dict } from '../i18n';

interface LangCtx {
  lang: Lang;
  t: Dict;
  toggle: () => void;
}

const Ctx = createContext<LangCtx | null>(null);

export function LangProvider({ children }: { children: ReactNode }) {
  const [lang, setLang] = useState<Lang>(() => (localStorage.getItem('vox_lang') === 'ar' ? 'ar' : 'en'));

  useEffect(() => {
    document.documentElement.lang = lang;
    document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
    localStorage.setItem('vox_lang', lang);
  }, [lang]);

  const toggle = () => setLang((l) => (l === 'en' ? 'ar' : 'en'));

  return <Ctx.Provider value={{ lang, t: dicts[lang], toggle }}>{children}</Ctx.Provider>;
}

export function useLang(): LangCtx {
  const ctx = useContext(Ctx);
  if (!ctx) throw new Error('useLang must be used within LangProvider');
  return ctx;
}
