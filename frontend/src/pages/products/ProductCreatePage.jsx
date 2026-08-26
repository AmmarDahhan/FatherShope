import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { createProduct } from '../../api/products'
import PageHeader from '../../components/ui/PageHeader'

export default function ProductCreatePage() {
  const navigate = useNavigate()
  const [form, setForm] = useState({
    name: '',
    barcode: '',
    category: '',
    price: '',
    stock_quantity: '',
    min_stock_alert: '10',
    description: '',
  })
  const [errors, setErrors] = useState({})
  const [submitting, setSubmitting] = useState(false)

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErrors({})
    setSubmitting(true)
    try {
      await createProduct({
        ...form,
        price: parseFloat(form.price),
        stock_quantity: parseInt(form.stock_quantity, 10),
        min_stock_alert: parseInt(form.min_stock_alert, 10),
      })
      navigate('/products')
    } catch (err) {
      setErrors(err.response?.data?.errors || {})
    } finally {
      setSubmitting(false)
    }
  }

  const fields = [
    { name: 'name', label: 'Product Name', type: 'text', required: true },
    { name: 'barcode', label: 'Barcode', type: 'text' },
    { name: 'category', label: 'Category', type: 'text' },
    { name: 'price', label: 'Selling Price ($)', type: 'number', required: true },
    { name: 'stock_quantity', label: 'Stock Quantity', type: 'number', required: true },
    { name: 'min_stock_alert', label: 'Low Stock Alert Threshold', type: 'number' },
  ]

  return (
    <div>
      <PageHeader title="Add Product" description="Create a new product in your inventory" />

      <div className="mx-auto max-w-2xl rounded-xl border border-gray-200 bg-white p-6">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {fields.map((field) => (
              <div key={field.name}>
                <label className="block text-sm font-medium text-gray-700">
                  {field.label} {field.required && <span className="text-red-500">*</span>}
                </label>
                <input
                  type={field.type}
                  name={field.name}
                  value={form[field.name]}
                  onChange={handleChange}
                  required={field.required}
                  step={field.type === 'number' ? 'any' : undefined}
                  className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                {errors[field.name] && (
                  <p className="mt-1 text-xs text-red-600">{errors[field.name][0]}</p>
                )}
              </div>
            ))}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              name="description"
              value={form.description}
              onChange={handleChange}
              rows={3}
              className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            />
            {errors.description && (
              <p className="mt-1 text-xs text-red-600">{errors.description[0]}</p>
            )}
          </div>

          <div className="flex items-center gap-3 pt-2">
            <button
              type="submit"
              disabled={submitting}
              className="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
            >
              {submitting ? 'Creating...' : 'Create Product'}
            </button>
            <button
              type="button"
              onClick={() => navigate('/products')}
              className="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}
