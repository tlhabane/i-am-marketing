import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  Mail, 
  CheckCircle2, 
  ArrowRight, 
  MapPin, 
  Phone,
  LayoutGrid,
  Palette,
  Printer,
  Calendar,
  Monitor
} from 'lucide-react';
import Logo from './assets/Logo_1213de0d.png';

// --- Constants & Types ---

const COLORS = {
  primary: '#4B2A5B',
  base: '#1F1027',
  accent: '#E5B84A',
  text: '#F8F9FA',
};

const LAUNCH_DATE = new Date('2027-01-02T00:00:00+02:00');

interface TimeLeft {
  days: number;
  hours: number;
  minutes: number;
  seconds: number;
}

// --- Components ---

const CountdownCircle = ({ value, label, max }: { value: number; label: string; max: number }) => {
  const radius = 42;
  const circumference = 2 * Math.PI * radius;
  // Progress is inverse (decreases as time passes) or direct? 
  // Usually, for countdowns, we want the circle to empty or fill. 
  // Let's make it fill based on the remaining value relative to max.
  const progress = Math.min(value / max, 1);
  const offset = circumference - progress * circumference;

  return (
    <div className="flex flex-col items-center">
      <div className="relative w-24 h-24 md:w-32 md:h-32 flex items-center justify-center group">
        {/* Glow effect */}
        <div className="absolute inset-2 bg-[#E5B84A]/5 rounded-full blur-xl group-hover:bg-[#E5B84A]/10 transition-colors" />
        
        <svg className="absolute inset-0 w-full h-full -rotate-90 drop-shadow-[0_0_8px_rgba(229,184,74,0.1)]">
          {/* Background Track */}
          <circle
            cx="50%"
            cy="50%"
            r={radius}
            stroke="currentColor"
            strokeWidth="4"
            fill="transparent"
            className="text-white/5"
          />
          {/* Progress Indicator */}
          <motion.circle
            cx="50%"
            cy="50%"
            r={radius}
            stroke="#E5B84A"
            strokeWidth="4"
            fill="transparent"
            strokeDasharray={circumference}
            animate={{ strokeDashoffset: offset }}
            transition={{ 
              duration: 1, 
              ease: "linear" // Linear for seconds, but spring-like for others could be nice
            }}
            strokeLinecap="round"
          />
        </svg>
        
        <motion.div 
          key={value}
          initial={{ opacity: 0, scale: 0.9, y: 5 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          className="relative z-10 flex flex-col items-center"
        >
          <span className="text-2xl md:text-3xl font-black text-white tracking-tighter">
            {value.toString().padStart(2, '0')}
          </span>
        </motion.div>
      </div>
      <span className="mt-1 text-[10px] md:text-xs font-bold uppercase tracking-[0.25em] text-white/20">
        {label}
      </span>
    </div>
  );
};

const ServiceCard = ({ icon: Icon, title, description, delay }: { icon: any; title: string; description: string; delay: number }) => (
  <motion.div
    initial={{ opacity: 0, y: 20 }}
    whileInView={{ opacity: 1, y: 0 }}
    viewport={{ once: true }}
    transition={{ delay }}
    className="p-6 rounded-2xl bg-white/5 border border-white/10 hover:border-[#E5B84A]/30 transition-colors group"
  >
    <div className="w-12 h-12 rounded-lg bg-[#4B2A5B]/50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
      <Icon className="w-6 h-6 text-[#E5B84A]" />
    </div>
    <h3 className="text-lg font-semibold text-white mb-2">{title}</h3>
    <p className="text-sm text-white/60 leading-relaxed">{description}</p>
  </motion.div>
);

// --- Main App ---

export default function App() {
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');
  const [timeLeft, setTimeLeft] = useState<TimeLeft>({ days: 0, hours: 0, minutes: 0, seconds: 0 });

  useEffect(() => {
    const timer = setInterval(() => {
      const difference = +LAUNCH_DATE - +new Date();
      if (difference > 0) {
        setTimeLeft({
          days: Math.floor(difference / (1000 * 60 * 60 * 24)),
          hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
          minutes: Math.floor((difference / 1000 / 60) % 60),
          seconds: Math.floor((difference / 1000) % 60),
        });
      }
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!email || !email.includes('@')) {
      setStatus('error');
      return;
    }
    setStatus('loading');
    // Simulate API call
    setTimeout(() => {
      setStatus('success');
      setEmail('');
    }, 1500);
  };

  const services = [
    {
      icon: LayoutGrid,
      title: "Strategic Marketing",
      description: "Data-driven advertising campaigns that reach your audience exactly where they are."
    },
    {
      icon: Palette,
      title: "Brand Design",
      description: "Crafting bold visual identities that resonate and stand the test of time."
    },
    {
      icon: Printer,
      title: "Litho & Digital Print",
      description: "High-impact print services for everything from corporate stationery to large scale media."
    },
    {
      icon: Calendar,
      title: "Corporate Events",
      description: "End-to-end management for exhibitions, conferences, and bespoke seminars."
    },
    {
      icon: Monitor,
      title: "Digital Media",
      description: "Modern signage and digital communications to keep your brand relevant and visible."
    }
  ];

  return (
    <div className="min-h-screen bg-[#1F1027] text-[#F8F9FA] selection:bg-[#E5B84A] selection:text-[#1F1027] overflow-x-hidden">
      {/* Background Effects */}
      <div className="fixed inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#4B2A5B] rounded-full blur-[120px] opacity-20 animate-pulse" />
        <div className="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-[#E5B84A] rounded-full blur-[120px] opacity-10" />
      </div>

      {/* Navigation */}
      <nav className="relative z-10 px-6 pt-12 flex justify-center max-w-7xl mx-auto">
        <div className="flex items-center">
          <img 
            src={Logo} 
            alt="I AM MARKETING Logo" 
            className="h-20 md:h-24 w-auto brightness-0 invert"
          />
        </div>
      </nav>

      <main className="relative z-10">
        {/* Hero Section */}
        <section className="px-6 pt-6 pb-20 md:pt-10 md:pb-32 text-center max-w-5xl mx-auto">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            
             {/* Eyebrow Badge */}
            <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-purple-900/60 to-purple-950/60 border border-[#E5B84A]/30 text-[#E5B84A] text-xs sm:text-sm font-semibold tracking-wider mb-4 shadow-lg shadow-purple-950/40 backdrop-blur-sm">
              <span className="relative flex h-2 w-2">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#E5B84A] opacity-75"></span>
                <span className="relative inline-flex rounded-full h-2 w-2 bg-[#E5B84A]"></span>
              </span>
              <span className="text-xs font-medium text-purple-200 tracking-[0.2em]">
                The Next Chapter in Integrated Marketing is Underway
              </span>
            </div>

            <h1 className="text-4xl md:text-7xl font-bold tracking-tight mb-6 bg-clip-text text-transparent bg-gradient-to-b from-white to-white/60">
              Crafting Bold Identities.<br />
              <span className="text-[#E5B84A]">Delivering Legendary Impact.</span>
            </h1>

            <p className="text-base md:text-xl text-white/60 max-w-2xl mx-auto mb-12 leading-relaxed">
              From strategic marketing and digital communications to high-impact Litho print 
              and corporate events.
            </p>

            {/* Countdown */}
            <div className="grid grid-cols-2 md:flex md:justify-center gap-8 md:gap-16 mb-20 max-w-3xl mx-auto">
              <CountdownCircle value={timeLeft.days} label="Days" max={60} />
              <CountdownCircle value={timeLeft.hours} label="Hours" max={60} />
              <CountdownCircle value={timeLeft.minutes} label="Mins" max={60} />
              <CountdownCircle value={timeLeft.seconds} label="Secs" max={60} />
            </div>

            {/* Lead Capture */}
            <div className="max-w-md mx-auto">
              <form onSubmit={handleSubmit} className="relative group">
                <div className="absolute -inset-1 bg-gradient-to-r from-[#4B2A5B] to-[#E5B84A] rounded-full blur opacity-20 group-focus-within:opacity-40 transition-opacity" />
                <div className="relative flex p-1 bg-white/5 border border-white/10 rounded-full backdrop-blur-xl">
                  <input
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="Enter your work email address..."
                    className="flex-1 bg-transparent px-6 py-3 outline-none text-white text-sm placeholder:text-white/30"
                    disabled={status === 'success' || status === 'loading'}
                  />
                  <button
                    type="submit"
                    disabled={status === 'success' || status === 'loading'}
                    className="bg-[#E5B84A] hover:bg-[#d4a840] text-[#1F1027] px-6 py-3 rounded-full text-sm font-bold flex items-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                  >
                    {status === 'loading' ? (
                      <div className="w-5 h-5 border-2 border-[#1F1027]/30 border-t-[#1F1027] rounded-full animate-spin" />
                    ) : status === 'success' ? (
                      <CheckCircle2 className="w-5 h-5" />
                    ) : (
                      <>
                        Get Notified
                        <ArrowRight className="w-4 h-4" />
                      </>
                    )}
                  </button>
                </div>
              </form>
              <AnimatePresence>
                {status === 'success' && (
                  <motion.p
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0 }}
                    className="mt-4 text-[#E5B84A] text-sm font-medium"
                  >
                    You're on the list! We'll notify you on launch day.
                  </motion.p>
                )}
                {status === 'error' && (
                  <motion.p
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0 }}
                    className="mt-4 text-red-400 text-sm font-medium"
                  >
                    Please enter a valid email address.
                  </motion.p>
                )}
              </AnimatePresence>
            </div>

            <div className="mt-20 pt-10 border-t border-white/5 hidden invisible">
              <p className="text-[10px] md:text-xs font-medium text-white/30 tracking-widest uppercase mb-6">
                Trusted by 100+ leading institutions nationwide
              </p>
              <div className="flex flex-wrap justify-center items-center gap-8 md:gap-12 opacity-30 grayscale contrast-125">
                {/* Generic logo placeholders for visual balance as requested for "Social Proof" */}
                <div className="h-6 md:h-8 w-24 bg-white/20 rounded-md" />
                <div className="h-6 md:h-8 w-32 bg-white/20 rounded-md" />
                <div className="h-6 md:h-8 w-20 bg-white/20 rounded-md" />
                <div className="h-6 md:h-8 w-28 bg-white/20 rounded-md" />
              </div>
            </div>
          </motion.div>
        </section>

        {/* Services Section */}
        <section className="px-6 py-20 bg-white/[0.02] hidden invisible">
          <div className="max-w-7xl mx-auto">
            <div className="mb-12 text-center md:text-left">
              <h2 className="text-2xl md:text-3xl font-bold text-white mb-4">Our Core Expertise</h2>
              <p className="text-white/40 max-w-lg">
                We deliver a comprehensive suite of marketing and print solutions tailored for modern business growth.
              </p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
              {services.map((service, index) => (
                <ServiceCard 
                  key={index}
                  icon={service.icon}
                  title={service.title}
                  description={service.description}
                  delay={index * 0.1}
                />
              ))}
            </div>
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="relative z-10 px-6 py-16 border-t border-white/5 hidden invisible">
        <div className="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12">
          <div className="space-y-6">
            <img 
              src="/assets/aistudio/Logo_1213de0d.png" 
              alt="I AM MARKETING Logo" 
              className="h-8 md:h-10 w-auto brightness-0 invert"
            />
            <p className="text-sm text-white/40 leading-relaxed max-w-xs">
              A 100% women-owned dynamic marketing company committed to creativity and professionalism since 2007.
            </p>
          </div>
          
          <div className="space-y-6">
            <h4 className="text-xs font-bold uppercase tracking-widest text-[#E5B84A]">Contact Information</h4>
            <ul className="space-y-4">
              <li className="flex items-start gap-3 group cursor-default">
                <MapPin className="w-5 h-5 text-white/20 group-hover:text-[#E5B84A] transition-colors shrink-0" />
                <span className="text-sm text-white/60 leading-tight">
                  173 Aquamarine Drive, Pebblerock Golf Village,<br />
                  Kameeldrift, Pretoria, 0035
                </span>
              </li>
              <li className="flex items-center gap-3 group">
                <Phone className="w-5 h-5 text-white/20 group-hover:text-[#E5B84A] transition-colors shrink-0" />
                <a href="tel:+27832779549" className="text-sm text-white/60 hover:text-white transition-colors">
                  +27 83 277 9549
                </a>
              </li>
              <li className="flex items-center gap-3 group">
                <Mail className="w-5 h-5 text-white/20 group-hover:text-[#E5B84A] transition-colors shrink-0" />
                <a href="mailto:hello@iam.za.bz" className="text-sm text-white/60 hover:text-white transition-colors">
                  hello@iam.za.bz
                </a>
              </li>
            </ul>
          </div>

          <div className="space-y-6">
            <h4 className="text-xs font-bold uppercase tracking-widest text-[#E5B84A]">Quick Links</h4>
            <div className="flex flex-wrap gap-x-8 gap-y-4">
              <a href="#" className="text-sm text-white/40 hover:text-[#E5B84A] transition-colors">About Us</a>
              <a href="#" className="text-sm text-white/40 hover:text-[#E5B84A] transition-colors">Portfolio</a>
              <a href="#" className="text-sm text-white/40 hover:text-[#E5B84A] transition-colors">Privacy Policy</a>
              <a href="#" className="text-sm text-white/40 hover:text-[#E5B84A] transition-colors">Terms of Service</a>
            </div>
            <div className="pt-4">
              <p className="text-[10px] text-white/20 uppercase tracking-tighter">
                &copy; {new Date().getFullYear()} I AM MARKETING. All rights reserved.
              </p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
