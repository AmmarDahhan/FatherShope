import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { getStats, getInventoryAlerts } from '../../api/dashboard'
import PageHeader from '../../components/ui/PageHeader'
import LoadingSpinner from '../../components/ui/LoadingSpinner'
import Badge from '../../components/ui/Badge'

export default function DashboardPage() {
  const [stats, setStats] = useState(null)
  const [alerts, setAlerts] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    Promise.all([getStats(), getInventoryAlerts()])
      .then(([statsRes, alertsRes]) => {
        setStats(statsRes.data)
        setAlerts(alertsRes.data.products || [])
      })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  if (loading) return <LoadingSpinner />

  const cards = [
    { label: 'Revenue Today', value: `$${stats?.daily?.revenue ?? 0}`, icon: '💰' },
    { label: 'Today\'s Transactions', value: stats?.daily?.sales_count ?? 0, icon: '🧾' },
    { label: 'Total Products', value: stats?.products?.total ?? 0, icon: '📦' },
    { label: 'Low Stock Items', value: stats?.products?.low_stock ?? 0, icon: '⚠️' },
  ]

  return (
    <div>
      <PageHeader title="Dashboard" description="Overview of your store">
        <Link
          to="/sales/new"
          className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
        >
          + New Sale
        </Link>
      </PageHeader>

      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {cards.map((card) => (
          <div key={card.label} className="rounded-xl border border-gray-200 bg-white p-6">
            <div className="flex items-center justify-between">
              <span className="text-2xl">{card.icon}</span>
            </div>
            <p className="mt-4 text-2xl font-bold text-gray-900">{card.value}</p>
            <p className="text-sm text-gray-500">{card.label}</p>
          </div>
        ))}
      </div>

      {alerts.length > 0 && (
        <div className="mt-8">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Inventory Alerts</h2>
          <div className="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {alerts.map((item) => (
                  <tr key={item.id}>
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">{item.name}</td>
                    <td className="px-6 py-4 text-sm text-gray-500">{item.stock_quantity}</td>
                    <td className="px-6 py-4">
                      <Badge color={item.stock_quantity === 0 ? 'danger' : 'warning'}>
                        {item.stock_quantity === 0 ? 'Out of Stock' : 'Low Stock'}
                      </Badge>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  )
}
