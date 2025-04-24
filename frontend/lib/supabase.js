import { createClient } from '@supabase/supabase-js'

const supabaseUrl = 'https://cihvgzdxdtuogtwnlfpx.supabase.co'
const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImNpaHZnemR4ZHR1b2d0d25sZnB4Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDIyOTc3NzUsImV4cCI6MjA1Nzg3Mzc3NX0.e14l93bT_EZzHHWkbLHLpUsGj92OgXqBynXyGv1X7Q4'

export const supabase = createClient(supabaseUrl, supabaseAnonKey)
