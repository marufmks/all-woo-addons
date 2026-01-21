import React, { useState } from 'react'
import Dashboard from './Dashboard'
import Settings from './Settings'

const App = () => {
  const [currentPage, setCurrentPage] = useState('dashboard')

  const strings = window.allWooAddonsAdmin?.strings || {}

  return (
    <div style={{
      backgroundColor: '#f6f7f7',
      minHeight: '100vh'
    }}>
      <div style={{
        background: 'linear-gradient(135deg, #2271b1 0%, #135e96 100%)',
        boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
        color: '#fff',
        marginBottom: '2rem',
        padding: '2rem 2rem 1rem'
      }}>
        <h1 style={{
          fontSize: '2rem',
          fontWeight: 600,
          margin: 0
        }}>All Woo Addons</h1>
        <nav style={{
          display: 'flex',
          gap: '1rem',
          marginTop: '1rem'
        }}>
          <button
            style={{
              borderRadius: '6px',
              color: currentPage === 'dashboard' ? '#fff' : 'rgba(255,255,255,0.8)',
              cursor: 'pointer',
              fontWeight: currentPage === 'dashboard' ? '600' : '500',
              padding: '0.5rem 1rem',
              textDecoration: 'none',
              backgroundColor: currentPage === 'dashboard' ? 'rgba(255,255,255,0.25)' : 'transparent',
              border: 'none',
              transition: 'all 0.3s ease'
            }}
            onClick={() => setCurrentPage('dashboard')}
            onMouseEnter={(e) => {
              if (currentPage !== 'dashboard') {
                e.target.style.backgroundColor = 'rgba(255,255,255,0.15)'
              }
            }}
            onMouseLeave={(e) => {
              if (currentPage !== 'dashboard') {
                e.target.style.backgroundColor = 'transparent'
              }
            }}
          >
            {strings.dashboard || 'Dashboard'}
          </button>
          <button
            style={{
              borderRadius: '6px',
              color: currentPage === 'settings' ? '#fff' : 'rgba(255,255,255,0.8)',
              cursor: 'pointer',
              fontWeight: currentPage === 'settings' ? '600' : '500',
              padding: '0.5rem 1rem',
              textDecoration: 'none',
              backgroundColor: currentPage === 'settings' ? 'rgba(255,255,255,0.25)' : 'transparent',
              border: 'none',
              transition: 'all 0.3s ease'
            }}
            onClick={() => setCurrentPage('settings')}
            onMouseEnter={(e) => {
              if (currentPage !== 'settings') {
                e.target.style.backgroundColor = 'rgba(255,255,255,0.15)'
              }
            }}
            onMouseLeave={(e) => {
              if (currentPage !== 'settings') {
                e.target.style.backgroundColor = 'transparent'
              }
            }}
          >
            {strings.settings || 'Settings'}
          </button>
        </nav>
      </div>

      <div style={{
        margin: '0 auto',
        maxWidth: '1400px',
        padding: '0 2rem 2rem'
      }}>
        {currentPage === 'dashboard' ? <Dashboard /> : <Settings />}
      </div>
    </div>
  )
}

export default App
