import React from 'react';
import { Link } from 'react-router-dom';
import Layout from '@/components/layout/Layout';

const Login: React.FC = () => {
  return (
    <Layout>
      <div className="py-16 container-custom">
        <div className="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
          <h1 className="text-3xl font-bold mb-6 text-center">Sign In</h1>
          <p className="text-gray-600 text-center mb-8">
            Welcome back! Please sign in to your account.
          </p>

          <form className="space-y-6">
            <div>
              <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-1">
                Email
              </label>
              <input
                id="email"
                type="email"
                required
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-tourtastic-blue"
                placeholder="your@email.com"
              />
            </div>

            <div>
              <div className="flex items-center justify-between mb-1">
                <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                  Password
                </label>
                <Link to="/forgot-password" className="text-sm text-tourtastic-blue hover:text-tourtastic-dark-blue">
                  Forgot Password?
                </Link>
              </div>
              <input
                id="password"
                type="password"
                required
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-tourtastic-blue"
                placeholder="••••••••"
              />
            </div>

            <div className="flex items-center">
              <input
                id="remember-me"
                type="checkbox"
                className="h-4 w-4 text-tourtastic-blue focus:ring-tourtastic-blue border-gray-300 rounded"
              />
              <label htmlFor="remember-me" className="ml-2 block text-sm text-gray-700">
                Remember me
              </label>
            </div>

            <button
              type="submit"
              className="w-full py-2 px-4 bg-tourtastic-blue hover:bg-tourtastic-dark-blue text-white rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-tourtastic-blue focus:ring-opacity-50"
            >
              Sign In
            </button>
          </form>

          <div className="mt-6 text-center">
            <p className="text-sm text-gray-600">
              Don't have an account?{' '}
              <Link to="/register" className="text-tourtastic-blue hover:text-tourtastic-dark-blue font-medium">
                Register
              </Link>
            </p>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default Login;
