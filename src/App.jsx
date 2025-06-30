import React from 'react';
import SafeIcon from './common/SafeIcon';
import * as FiIcons from 'react-icons/fi';

const { FiCheckCircle, FiDownload, FiSettings } = FiIcons;

function App() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-8">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            WhatsApp Conversations Module
          </h1>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Professional Perfex CRM module for managing WhatsApp conversations within customer profiles
          </p>
        </div>

        {/* Features Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
          {[
            {
              icon: FiCheckCircle,
              title: "Customer Integration",
              description: "Seamlessly integrates with existing customer profiles as a new tab"
            },
            {
              icon: FiSettings,
              title: "Full Permissions",
              description: "Complete permission system with view, create, edit, and delete controls"
            },
            {
              icon: FiDownload,
              title: "Easy Installation",
              description: "Standard Perfex module installation process with automatic database setup"
            }
          ].map((feature, index) => (
            <div key={index} className="bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition-shadow">
              <div className="flex items-center mb-4">
                <SafeIcon 
                  icon={feature.icon} 
                  className="text-2xl text-indigo-600 mr-3" 
                />
                <h3 className="text-lg font-semibold text-gray-900">
                  {feature.title}
                </h3>
              </div>
              <p className="text-gray-600">
                {feature.description}
              </p>
            </div>
          ))}
        </div>

        {/* Installation Steps */}
        <div className="bg-white rounded-lg shadow-md p-8 mb-8">
          <h2 className="text-2xl font-bold text-gray-900 mb-6">Installation Steps</h2>
          <div className="space-y-4">
            {[
              "Upload the 'whatsapp_conversations' folder to your Perfex CRM modules directory",
              "Navigate to Setup → Modules in your admin panel",
              "Find 'WhatsApp Conversations' and click Install",
              "Configure permissions under Setup → Staff → Roles",
              "The tab will appear in customer profiles immediately"
            ].map((step, index) => (
              <div key={index} className="flex items-start">
                <div className="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
                  {index + 1}
                </div>
                <p className="text-gray-700 pt-1">{step}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Module Location */}
        <div className="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded">
          <div className="flex">
            <SafeIcon icon={FiDownload} className="text-indigo-400 text-xl mr-3 flex-shrink-0 mt-0.5" />
            <div>
              <h3 className="text-lg font-medium text-indigo-800">Module Location</h3>
              <p className="text-indigo-700 mt-1">
                The complete Perfex CRM module is located in the <code className="bg-indigo-100 px-2 py-1 rounded">perfex-crm-modules/</code> directory.
                This is separate from the React application and ready for deployment to your Perfex CRM installation.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;