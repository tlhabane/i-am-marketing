import React from 'react';
import { motion } from 'motion/react';

export const PageLoadingSpinner = () => {
    return (
        <motion.div
            key="loader"
            initial={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.5 }}
            className="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-[#1F1027]"
        >
            <div className="relative">
                <motion.div
                    animate={{ rotate: 360 }}
                    transition={{ duration: 2, repeat: Infinity, ease: "linear" }}
                    className="w-24 h-24 rounded-full border-2 border-white/5 border-t-[#E5B84A]"
                />
                <div className="absolute inset-0 flex items-center justify-center">
                    <div className="w-12 h-12 bg-[#4B2A5B]/20 rounded-full animate-pulse" />
                </div>
            </div>
            <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.2 }}
                className="mt-8 flex flex-col items-center gap-2"
            >
                <span className="text-[#E5B84A] font-bold tracking-[0.3em] uppercase text-[10px]">I Am Marketing</span>
                <div className="h-[2px] w-12 bg-white/10 overflow-hidden rounded-full">
                    <motion.div
                        animate={{ x: [-48, 48] }}
                        transition={{ duration: 1.5, repeat: Infinity, ease: "easeInOut" }}
                        className="w-1/2 h-full bg-[#E5B84A]"
                    />
                </div>
            </motion.div>
        </motion.div>
    );
};