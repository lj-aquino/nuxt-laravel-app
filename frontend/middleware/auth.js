import { supabase } from '~/lib/supabase'

export default defineNuxtRouteMiddleware(async (to) => {
  try {
    // Get the current session
    const { data: { session } } = await supabase.auth.getSession()
    
    // List of public routes that don't require authentication
    const publicRoutes = ['/', '/login', '/signup', '/about', '/contact']
    
    // If user is not authenticated and trying to access a protected route
    if (!session && !publicRoutes.includes(to.path)) {
      return navigateTo('/login')
    }
  } catch (error) {
    // Handle any errors in the authentication process
    console.error('Authentication error:', error)
    // Still redirect to login page if there's an error
    return navigateTo('/login')
  }
})