
import React, { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { Menu, X, Bell, Globe } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useLocale } from '@/hooks/useLocale';
import Logo from '@/assets/logo';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';

const Header: React.FC = () => {
  const { t } = useTranslation();
  const { currentLocale, toggleLocale } = useLocale();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [hasUnreadNotifications, setHasUnreadNotifications] = useState(true);

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  return (
    <header className="bg-white shadow-md fixed w-full top-0 left-0 z-50">
      <div className="container-custom mx-auto py-3 flex items-center justify-between">
        <Link to="/" className="flex items-center">
          <Logo />
        </Link>

        {/* Mobile menu button */}
        <div className="md:hidden">
          <button
            onClick={toggleMenu}
            className="text-gray-800 hover:text-tourtastic-blue focus:outline-none"
          >
            {isMenuOpen ? (
              <X size={24} />
            ) : (
              <Menu size={24} />
            )}
          </button>
        </div>

        {/* Desktop Navigation */}
        <nav className="hidden md:flex items-center space-x-1">
          <NavLink to="/" className={({isActive}) => `nav-link ${isActive ? 'nav-link-active' : ''}`} end>
            {t('home')}
          </NavLink>
          <NavLink to="/flights" className={({isActive}) => `nav-link ${isActive ? 'nav-link-active' : ''}`}>
            {t('flights')}
          </NavLink>
          <NavLink to="/destinations" className={({isActive}) => `nav-link ${isActive ? 'nav-link-active' : ''}`}>
            {t('destinations')}
          </NavLink>
          <NavLink to="/about" className={({isActive}) => `nav-link ${isActive ? 'nav-link-active' : ''}`}>
            {t('about')}
          </NavLink>
          <NavLink to="/contact" className={({isActive}) => `nav-link ${isActive ? 'nav-link-active' : ''}`}>
            {t('contact')}
          </NavLink>
        </nav>

        {/* Auth Buttons & Utilities Desktop */}
        <div className="hidden md:flex items-center space-x-3">

          
          {/* Notifications */}
          <Button
            variant="ghost"
            size="icon"
            className="text-gray-600 hover:text-tourtastic-blue relative"
            asChild
          >
            <Link to="/notifications">
              <Bell className="h-5 w-5" />
              {hasUnreadNotifications && (
                <span className="absolute top-0 right-0 block h-2 w-2 rounded-full bg-tourtastic-blue ring-2 ring-white" />
              )}
              <span className="sr-only">{t('notifications')}</span>
            </Link>
          </Button>
          
          {/* Language dropdown */}
          <Button 
            variant="outline"
            size="sm"
            className="h-8 px-2 flex items-center gap-1"
            onClick={toggleLocale}
          >
            <Globe className="h-4 w-4" />
            {currentLocale === 'en' ? 'EN' : 'AR'}
          </Button>
          
          <Link to="/login" className="text-tourtastic-blue hover:text-tourtastic-dark-blue transition-colors">
            {t('signIn')}
          </Link>
          <Link to="/register" className="btn-primary">
            {t('register')}
          </Link>
        </div>

        {/* Mobile Navigation */}
        {isMenuOpen && (
          <div className="md:hidden absolute top-full left-0 right-0 bg-white shadow-md animate-fade-in">
            <div className="container mx-auto py-3 flex flex-col">
              <NavLink 
                to="/" 
                className={({isActive}) => `py-2 px-4 ${isActive ? 'text-tourtastic-blue font-semibold' : 'text-gray-800'}`}
                onClick={toggleMenu}
                end
              >
                {t('home')}
              </NavLink>
              <NavLink 
                to="/flights" 
                className={({isActive}) => `py-2 px-4 ${isActive ? 'text-tourtastic-blue font-semibold' : 'text-gray-800'}`}
                onClick={toggleMenu}
              >
                {t('flights')}
              </NavLink>
              <NavLink 
                to="/destinations" 
                className={({isActive}) => `py-2 px-4 ${isActive ? 'text-tourtastic-blue font-semibold' : 'text-gray-800'}`}
                onClick={toggleMenu}
              >
                {t('destinations')}
              </NavLink>
              <NavLink 
                to="/about" 
                className={({isActive}) => `py-2 px-4 ${isActive ? 'text-tourtastic-blue font-semibold' : 'text-gray-800'}`}
                onClick={toggleMenu}
              >
                {t('about')}
              </NavLink>
              <NavLink 
                to="/contact" 
                className={({isActive}) => `py-2 px-4 ${isActive ? 'text-tourtastic-blue font-semibold' : 'text-gray-800'}`}
                onClick={toggleMenu}
              >
                {t('contact')}
              </NavLink>
              
              <NavLink 
                to="/notifications" 
                className={({isActive}) => `py-2 px-4 ${isActive ? 'text-tourtastic-blue font-semibold' : 'text-gray-800'}`}
                onClick={toggleMenu}
              >
                {t('notifications')}
                {hasUnreadNotifications && (
                  <span className="ml-2 inline-block h-2 w-2 rounded-full bg-red-500" />
                )}
              </NavLink>
              
              <div className="flex items-center justify-between py-2 px-4">
                <span className="text-gray-800">{currentLocale === 'en' ? 'Language' : 'اللغة'}</span>
                <Button 
                  variant="outline" 
                  size="sm" 
                  onClick={toggleLocale}
                >
                  {currentLocale === 'en' ? 'العربية' : 'English'}
                </Button>
              </div>
              
              <div className="mt-4 flex flex-col space-y-2 px-4">
                <Link 
                  to="/login" 
                  className="text-tourtastic-blue hover:text-tourtastic-dark-blue py-2"
                  onClick={toggleMenu}
                >
                  {t('signIn')}
                </Link>
                <Link 
                  to="/register" 
                  className="btn-primary text-center"
                  onClick={toggleMenu}
                >
                  {t('register')}
                </Link>
              </div>
            </div>
          </div>
        )}
      </div>
    </header>
  );
};

export default Header;
