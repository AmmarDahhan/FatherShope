import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { getSales, getSale } from '../../api/sales'
import PageHeader from '../../components/ui/PageHeader'
import LoadingSpinner from '../../components/ui/LoadingSpinner'
import EmptyState from '../../components/ui/EmptyState'

export default function SalesPage() {
  const [sales, setSales] = useState([])
  const [loading, setLoading] = useState(true)
  const [page, setPage] = useState(1)
  const [selectedSale, setSelectedSale] = useState(null)

  useEffect(() => {
    setLoading(true)
    getSales({ page })
      .then((res) => setSales(res.data.data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [page])

  const viewSale = async (id) => {
    const res = await getSale(id)
    setSelectedSale(res.data.data)
  }

  return (
    <div>
      <PageHeader title="Sales" description="View transaction history">
        <Link
          to="/sales/new"
          className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
        >
          + New Sale
        </Link>
      </PageHeader>

      {loading ? (
        <LoadingSpinner />
      ) : sales.length === 0 ? (
        <EmptyState title="No sales yet" description="Sales will appear here once processed" />
      ) : (
        <>
          <div className="rounded-xl border border-gray-200 bg-white overflow-hidden">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cashier</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {sales.map((sale) => (
                  <tr key={sale.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">#{String(sale.id).slice(0, 8)}</td>
                    <td className="px-6 py-4 text-sm text-gray-500">{sale.cashier_name}</td>
                    <td className="px-6 py-4 text-sm text-gray-500">{sale.items?.length ?? 0}</td>
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">${sale.total}</td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {new Date(sale.created_at).toLocaleDateString()}
                    </td>
                    <td className="px-6 py-4 text-sm">
                      <button
                        onClick={() => viewSale(sale.id)}
                        className="text-indigo-600 hover:text-indigo-800"
                      >
                        View
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="flex items-center justify-between border-t border-gray-200 px-6 py-3">
            <button
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page <= 1}
              className="rounded-lg border border-gray-300 px-3 py-1.5 text-sm disabled:opacity-50"
            >
              Previous
            </button>
            <span className="text-sm text-gray-500">Page {page}</span>
            <button
              onClick={() => setPage((p) => p + 1)}
              disabled={sales.length < 15}
              className="rounded-lg border border-gray-300 px-3 py-1.5 text-sm disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </>
      )}

      {selectedSale && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50" onClick={() => setSelectedSale(null)}>
          <div className="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl" onClick={(e) => e.stopPropagation()}>
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-lg font-semibold">Sale #{String(selectedSale.id).slice(0, 8)}</h2>
              <button onClick={() => setSelectedSale(null)} className="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div className="space-y-2 text-sm">
              <p><span className="font-medium">Cashier:</span> {selectedSale.cashier_name}</p>
              <p><span className="font-medium">Date:</span> {new Date(selectedSale.created_at).toLocaleString()}</p>
              <p><span className="font-medium">Subtotal:</span> ${selectedSale.subtotal}</p>
              <p><span className="font-medium">Tax:</span> ${selectedSale.tax}</p>
              <p className="text-base font-bold"><span>Total:</span> ${selectedSale.total}</p>
            </div>
            <table className="mt-4 min-w-full text-sm">
              <thead>
                <tr className="border-b text-left text-xs text-gray-500 uppercase">
                  <th className="py-2">Product</th>
                  <th className="py-2">Qty</th>
                  <th className="py-2">Price</th>
                  <th className="py-2">Total</th>
                </tr>
              </thead>
              <tbody>
                {selectedSale.items?.map((item) => (
                  <tr key={item.id} className="border-b">
                    <td className="py-2">{item.product_name}</td>
                    <td className="py-2">{item.quantity}</td>
                    <td className="py-2">${item.unit_price}</td>
                    <td className="py-2">${item.total_price}</td>
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
