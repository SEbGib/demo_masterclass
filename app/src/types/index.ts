// User type (matches API)
export interface User {
  id: string
  email: string
  roles: string[]
  createdAt: string
}

// Task type (to be implemented via issues)
export interface Task {
  id: string
  title: string
  status: TaskStatus
  priority?: TaskPriority
  dueDate?: string
  createdAt: string
  updatedAt: string
}

export type TaskStatus = 'pending' | 'in_progress' | 'done'
export type TaskPriority = 'low' | 'medium' | 'high' | 'urgent'

// API response types
export interface ApiCollection<T> {
  'hydra:member': T[]
  'hydra:totalItems': number
}

export interface ApiError {
  'hydra:title': string
  'hydra:description': string
  violations?: Array<{
    propertyPath: string
    message: string
  }>
}
