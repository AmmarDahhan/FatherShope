import api from './axios'

export const getStats = () => api.get('/dashboard/stats')
export const getInventoryAlerts = () => api.get('/dashboard/inventory-alerts')
