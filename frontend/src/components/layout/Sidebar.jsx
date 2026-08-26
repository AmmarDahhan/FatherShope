import { NavLink } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'

const nav = [
  { to: '/', label: 'Dashboard', icon: '📊' },
  { to: '/products', label: 'Products', icon: '📦' },
  { to: '/sales', label: 'Sales', icon: '💰' },
  { to: '/sales/new', label: 'New Sale', icon: '🧾' },
  { to: '/users', label: 'Users', icon: '👥', managerOnly: true },
]

export default function Sidebar() {
  const { user } = useAuth()

  return (
    <aside className="fixed inset-y-0 left-0 z-50 w-64 border-r border-gray-200 bg-white">
      <div className="flex h-16 items-center gap-2 border-b border-gray-200 px-6">
        <span className="text-2xl">🏪</span>
        <span className="text-lg font-bold text-gray-900">FatherShope</span>
      </div>

      <nav className="mt-4 px-3 space-y-1">
        {nav
          .filter((item) => !item.managerOnly || user?.role === 'manager')
          .map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              end={item.to === '/'}
              className={({ isActive }) =>
                `flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors ${
                  isActive
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-gray-700 hover:bg-gray-50'
                }`
              }
            >
              <span>{item.icon}</span>
              {item.label}
            </NavLink>
          ))}
      </nav>
    </aside>
  )
}
