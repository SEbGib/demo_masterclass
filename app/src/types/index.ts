// Types will be added as features are implemented

// API response types (generic, ready for use)
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
