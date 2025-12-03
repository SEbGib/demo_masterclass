import type { ReactNode } from 'react'

interface LayoutProps {
  children: ReactNode
}

export function Layout({ children }: LayoutProps) {
  return (
    <div className="app-layout">
      <header className="app-header">
        <div className="header-content">
          <h1 className="app-title">Task Manager</h1>
          <p className="app-subtitle">Demo Masterclass - AI-Assisted Development</p>
        </div>
      </header>
      
      <main className="app-main">
        {children}
      </main>
      
      <footer className="app-footer">
        <p>Built with Symfony + API Platform + React</p>
      </footer>
    </div>
  )
}
