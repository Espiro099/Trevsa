import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'trevsa': {
                    red: '#FF0000',      // Rojo vivo para acciones principales
                    'red-soft': '#FF3333', // Rojo suave para hovers
                    'red-light': 'rgba(255, 0, 0, 0.1)', // Rojo claro para backgrounds
                    black: '#000000',    // Negro puro para texto principal
                    'black-soft': '#222222', // Negro suave para elementos secundarios
                    white: '#FFFFFF',    // Blanco puro para fondos
                    'white-soft': '#F5F5F5', // Blanco suave para cards y secciones
                    gray: {
                        50: '#FAFAFA',
                        100: '#F4F4F4',
                        200: '#E5E5E5',
                        300: '#D4D4D4',
                        400: '#A3A3A3',
                        500: '#737373',
                        600: '#525252',
                        700: '#404040',
                        800: '#262626',
                        900: '#171717',
                    }
                },
            },
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
            },
            fontSize: {
                '2xs': '0.625rem',
            },
            borderRadius: {
                'xl': '1rem',
                '2xl': '2rem',
            },
            transitionDuration: {
                'fast': '150ms',
                'base': '250ms',
                'slow': '350ms',
            },
            transitionTimingFunction: {
                'react': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
            boxShadow: {
                'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'base': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
                'md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'xl': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
            },
            animation: {
                'slide-in-right': 'slide-in-right 0.3s ease-out',
                'fade-in': 'fade-in 0.3s ease-out',
                'scale-in': 'scale-in 0.2s ease-out',
                'slide-up': 'slide-up 0.3s ease-out',
            },
            keyframes: {
                'slide-in-right': {
                    '0%': { transform: 'translateX(100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'scale-in': {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                'slide-up': {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
