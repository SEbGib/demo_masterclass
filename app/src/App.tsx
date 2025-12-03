import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { Layout } from './components/Layout'
import { EmptyState } from './components/EmptyState'
import './App.css'

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 5, // 5 minutes
      retry: 1,
    },
  },
})

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <Layout>
        <section className="content-section">
          <EmptyState 
            title="No Tasks Yet"
            description="Tasks will appear here once the Task entity is implemented. Check the GitHub issues for features to add!"
            icon="🎯"
          />
          
          <div className="info-card">
            <h3>Getting Started</h3>
            <p>This is the starting point for the AI-assisted development demo.</p>
            <ul>
              <li>Backend: <a href="http://localhost:8000/api" target="_blank" rel="noopener">http://localhost:8000/api</a></li>
              <li>Run <code>gh issue list</code> to see features to implement</li>
              <li>Use <code>/tdd-feature</code> to implement with TDD workflow</li>
            </ul>
          </div>
        </section>
      </Layout>
    </QueryClientProvider>
  )
}

export default App
