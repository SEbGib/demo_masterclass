interface EmptyStateProps {
  title: string
  description: string
  icon?: string
}

export function EmptyState({ title, description, icon = '📋' }: EmptyStateProps) {
  return (
    <div className="empty-state">
      <span className="empty-state-icon">{icon}</span>
      <h2 className="empty-state-title">{title}</h2>
      <p className="empty-state-description">{description}</p>
    </div>
  )
}
