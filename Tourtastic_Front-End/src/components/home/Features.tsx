
import React from 'react';
import { useTranslation } from 'react-i18next';
import { Globe, Lock, Phone } from 'lucide-react';

const Features: React.FC = () => {
  const { t } = useTranslation();
  
  const features = [
    {
      icon: <Globe className="h-12 w-12 text-tourtastic-blue" />,
      title: t('globalDestinations'),
      description: t('exploreHundreds')
    },
    {
      icon: <Lock className="h-12 w-12 text-tourtastic-blue" />,
      title: t('secureBooking'),
      description: t('bookWithConfidence')
    },
    {
      icon: <Phone className="h-12 w-12 text-tourtastic-blue" />,
      title: t('support'),
      description: t('customerSupport')
    }
  ];

  return (
    <section className="py-20 bg-gray-50">
      <div className="container-custom">
        <div className="text-center mb-12">
          <h2 className="section-title mx-auto">{t('whyChooseTourtastic')}</h2>
          <p className="text-gray-600 max-w-3xl mx-auto">
            {t('weProvide')}
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <div 
              key={index} 
              className="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 flex flex-col items-center text-center"
            >
              <div className="mb-4">
                {feature.icon}
              </div>
              <h3 className="text-xl font-bold mb-3">{feature.title}</h3>
              <p className="text-gray-600">{feature.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Features;
