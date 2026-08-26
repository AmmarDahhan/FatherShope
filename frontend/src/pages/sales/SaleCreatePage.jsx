import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { getProducts } from '../../api/products'
import { createSale } from '../../api/sales'
import PageHeader from '../../components/ui/PageHeader'
import LoadingSpinner from '../../components/ui/LoadingSpinner'

const TAX_RATE = 0.1

export default function SaleCreatePage() {
  const navigate = useNavigate()
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [cart, setCart] = useState([])
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    getProducts({ per_page: 100 })
      .then((res) => setProducts(res.data.data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  const filtered = products.filter(
    (p) =>
      p.is_active &&
      (p.name.toLowerCase().includes(search.toLowerCase()) ||
        p.barcode?.toLowerCase().includes(search.toLowerCase()))
  )

  const addToCart = (product) => {
    const existing = cart.find((item) => item.product_id === product.id)
    if (existing) {
      if (existing.quantity >= product.stock_quantity) return
      setCart(
        cart.map((item) =>
          item.product_id === product.id
            ? { ...item, quantity: item.quantity + 1 }
            : item
        )
      )
    } else {
      setCart([
        ...cart,
        {
          product_id: product.id,
          name: product.name,
          unit_price: parseFloat(product.price),
          stock_quantity: product.stock_quantity,
          quantity: 1,
        },
      ])
    }
    setSearch('')
  }

  const updateQuantity = (productId, qty) => {
    const num = Math.max(1, parseInt(qty, 10) || 1)
    const item = cart.find((i) => i.product_id === productId)
    if (item && num > item.stock_quantity) return
    setCart(
      cart.map((item) =>
        item.product_id === productId ? { ...item, quantity: num } : item
      )
    )
  }

  const removeFromCart = (productId) => {
    setCart(cart.filter((item) => item.product_id !== productId))
  }

  const subtotal = cart.reduce((sum, item) => sum + item.unit_price * item.quantity, 0)
  const tax = subtotal * TAX_RATE
  const total = subtotal + tax

  const handleSubmit = async () => {
    if (cart.length === 0) return
    setError('')
    setSubmitting(true)
    try {
      await createSale({
        items: cart.map((item) => ({
          product_id: item.product_id,
          quantity: item.quantity,
        })),
      })
      navigate('/sales')
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to create sale')
    } finally {
      setSubmitting(false)
    }
  }

  if (loading) return <LoadingSpinner />

  return (
    <div>
      <PageHeader title="New Sale" description="Process a new transaction" />

      <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div className="lg:col-span-2">
          <div className="rounded-xl border border-gray-200 bg-white p-4">
            <input
              type="text"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Search by name or barcode..."
              className="mb-4 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
              autoFocus
            />

            {search && (
              <div className="max-h-96 overflow-y-auto">
                {filtered.length === 0 ? (
                  <p className="py-4 text-center text-sm text-gray-500">No products found</p>
                ) : (
                  <div className="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    {filtered.map((product) => (
                      <button
                        key={product.id}
                        onClick={() => addToCart(product)}
                        className="flex items-center justify-between rounded-lg border border-gray-200 p-3 text-left hover:border-indigo-300 hover:bg-indigo-50"
                      >
                        <div>
                          <p className="text-sm font-medium text-gray-900">{product.name}</p>
                          <p className="text-xs text-gray-500">
                            {product.category} · Stock: {product.stock_quantity}
                          </p>
                        </div>
                        <span className="text-sm font-semibold text-indigo-600">
                          ${product.price}
                        </span>
                      </button>
                    ))}
                  </div>
                )}
              </div>
            )}

            {!search && (
              <div className="py-8 text-center text-sm text-gray-400">
                Type to search for products to add
              </div>
            )}
          </div>
        </div>

        <div className="lg:col-span-1">
          <div className="rounded-xl border border-gray-200 bg-white">
            <div className="border-b border-gray-200 px-4 py-3">
              <h2 className="text-sm font-semibold text-gray-900">
                Cart ({cart.length} items)
              </h2>
            </div>

            {cart.length === 0 ? (
              <div className="px-4 py-8 text-center text-sm text-gray-400">
                No items in cart
              </div>
            ) : (
              <>
                <div className="max-h-64 overflow-y-auto divide-y divide-gray-100">
                  {cart.map((item) => (
                    <div key={item.product_id} className="flex items-center justify-between px-4 py-3">
                      <div className="flex-1">
                        <p className="text-sm font-medium text-gray-900">{item.name}</p>
                        <p className="text-xs text-gray-500">${item.unit_price} each</p>
                      </div>
                      <div className="flex items-center gap-2">
                        <input
                          type="number"
                          min="1"
                          max={item.stock_quantity}
                          value={item.quantity}
                          onChange={(e) => updateQuantity(item.product_id, e.target.value)}
                          className="w-16 rounded border border-gray-300 px-2 py-1 text-center text-xs"
                        />
                        <span className="w-20 text-right text-sm font-medium text-gray-900">
                          ${(item.unit_price * item.quantity).toFixed(2)}
                        </span>
                        <button
                          onClick={() => removeFromCart(item.product_id)}
                          className="text-gray-400 hover:text-red-500"
                        >
                          ✕
                        </button>
                      </div>
                    </div>
                  ))}
                </div>

                <div className="border-t border-gray-200 px-4 py-3 space-y-1">
                  <div className="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>${subtotal.toFixed(2)}</span>
                  </div>
                  <div className="flex justify-between text-sm text-gray-600">
                    <span>Tax (10%)</span>
                    <span>${tax.toFixed(2)}</span>
                  </div>
                  <div className="flex justify-between text-base font-bold text-gray-900 pt-1">
                    <span>Total</span>
                    <span>${total.toFixed(2)}</span>
                  </div>
                </div>

                {error && (
                  <div className="mx-4 mb-3 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                    {error}
                  </div>
                )}

                <div className="px-4 pb-4">
                  <button
                    onClick={handleSubmit}
                    disabled={submitting}
                    className="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                  >
                    {submitting ? 'Processing...' : `Pay $${total.toFixed(2)}`}
                  </button>
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
