import React from 'react';
import { motion } from 'motion/react';

type Props = {
    value: number;
    label: string;
    max: number
}

const radius = 42;
const circumference = 2 * Math.PI * radius;

export const CountdownCircle: React.FC<Props> = ({ value, label, max }) => {
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