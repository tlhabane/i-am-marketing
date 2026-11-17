import React from 'react';
import { motion } from 'motion/react';

type Props = {
    icon: any;
    title: string;
    description: string;
    delay: number;
}

export const ServiceCard: React.FC<Props> = ({ icon: Icon, title, description, delay }) => (
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