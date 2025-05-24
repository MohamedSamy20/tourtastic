import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";

import Home from "./pages/Home";
import Flights from "./pages/Flights";
import Destinations from "./pages/Destinations";
import About from "./pages/About";
import Contact from "./pages/Contact";
import Login from "./pages/Login";
import Register from "./pages/Register";
import NotFound from "./pages/NotFound";
import ForgotPassword from "./pages/ForgotPassword";

// New pages
import Cart from "./pages/Cart";
import Profile from "./pages/Profile";
import Notifications from "./pages/Notifications";
import AdminLayout from "./components/layout/AdminLayout";
import AdminDashboard from "./pages/admin/Dashboard";
import AdminBookings from "./pages/admin/Bookings";
import AdminProfile from "./pages/admin/Profile";
import AdminUsers from "./pages/admin/Users";
import AdminDestinations from "./pages/admin/Destinations";
import AdminReports from "./pages/admin/Reports";

const queryClient = new QueryClient();

// Simple Auth check (to be replaced with real auth)
const isAuthenticated = () => {
  return true; // In a real app, this would check if user is logged in
};

const isAdmin = () => {
  return true; // In a real app, this would check if user has admin role
};

// Protected route component
const ProtectedRoute = ({ children, isAllowed }: { children: React.ReactNode; isAllowed: boolean }) => {
  if (!isAllowed) {
    return <Navigate to="/login" replace />;
  }
  return <>{children}</>;
};

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          {/* Public Routes */}
          <Route path="/" element={<Home />} />
          <Route path="/flights" element={<Flights />} />
          <Route path="/destinations" element={<Destinations />} />
          <Route path="/about" element={<About />} />
          <Route path="/contact" element={<Contact />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/forgot-password" element={<ForgotPassword />} />
          <Route path="/notifications" element={<Notifications />} />
          
          {/* Protected User Routes */}
          <Route 
            path="/cart" 
            element={
              <ProtectedRoute isAllowed={isAuthenticated()}>
                <Cart />
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/profile" 
            element={
              <ProtectedRoute isAllowed={isAuthenticated()}>
                <Profile />
              </ProtectedRoute>
            } 
          />
          
          {/* Protected Admin Routes */}
          <Route
            path="/admin"
            element={
              <ProtectedRoute isAllowed={isAuthenticated() && isAdmin()}>
                <AdminLayout>
                  <AdminDashboard />
                </AdminLayout>
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/bookings"
            element={
              <ProtectedRoute isAllowed={isAuthenticated() && isAdmin()}>
                <AdminLayout>
                  <AdminBookings />
                </AdminLayout>
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/profile"
            element={
              <ProtectedRoute isAllowed={isAuthenticated() && isAdmin()}>
                <AdminProfile />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/users"
            element={
              <ProtectedRoute isAllowed={isAuthenticated() && isAdmin()}>
                <AdminUsers />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/destinations"
            element={
              <ProtectedRoute isAllowed={isAuthenticated() && isAdmin()}>
                <AdminDestinations />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/reports"
            element={
              <ProtectedRoute isAllowed={isAuthenticated() && isAdmin()}>
                <AdminLayout>
                  <AdminReports />
                </AdminLayout>
              </ProtectedRoute>
            }
          />


          {/* 404 Route */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
