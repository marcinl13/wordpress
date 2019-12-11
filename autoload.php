<?php

#region interfaces

include_once INTERFACE_PATH . "IUnits.php";
include_once INTERFACE_PATH . "IPaymentsGates.php";
include_once INTERFACE_PATH . "IPaymentsStatus.php";
include_once INTERFACE_PATH . "IShortCodes.php";
include_once INTERFACE_PATH . "ITablesNames.php";
include_once INTERFACE_PATH . "IHttpStatusCode.php";
include_once INTERFACE_PATH . "IDocuments.php";
include_once INTERFACE_PATH . "ILanguages.php";

//v18
include_once INTERFACE_PATH . "IOrderStatus.php";
include_once INTERFACE_PATH . "IPDF.php";
include_once INTERFACE_PATH . "IDoc.php";

//v19
include_once INTERFACE_PATH . "IDBDataFactor.php";
#endregion

#region models

include_once MODELS_PATH . "modelPaymentPayU.php";
include_once MODELS_PATH . "modelCategory.php";
include_once MODELS_PATH . "modelProduct.php";
include_once MODELS_PATH . "modelOrder.php";
include_once MODELS_PATH . "modelVat.php";

//v17

//v18
include_once MODELS_PATH . "modelMagazine18.php";
include_once MODELS_PATH . "modelInvoices18.php";
include_once MODELS_PATH . "modelDocuments18.php";


include_once MODELS_PATH . "Token.php";
#endregion

#region restApi

include_once REST_API . "cToken.php";
include_once REST_API . "cCategory.php";
include_once REST_API . "cProducts.php";
include_once REST_API . "cOrder.php";
include_once REST_API . "cCart.php";
include_once REST_API . "cVat.php";
include_once REST_API . "cMagazine.php";
include_once REST_API . "cDocs.php";
// include_once REST_API . "Invoice.php";
include_once REST_API . "restAccess.php"; //
include_once REST_API . "RestAPI.php";  //
#endregion

#region Payments
include_once PAYMENTS . "PayU.php";
include_once PAYMENTS . "FactoryPaymentGate.php";
#endregion

#region DB
include_once TABLE_DB . "cTabelki.php"; //
include_once TABLE_DB . "tCategory.php";
include_once TABLE_DB . "tOrders.php";
include_once TABLE_DB . "tProducts.php";
include_once TABLE_DB . "tTransactions.php";
include_once TABLE_DB . "tVat.php";

//v18
include_once TABLE_DB . "tDocuments18.php";
include_once TABLE_DB . "tMagazine18.php";
include_once TABLE_DB . "tInvoices.php";


include_once TABLE_DB . "cTables.php"; //

//v19
include_once TABLE_DB ."DBConnection.php";
#endregion

#region other
include_once CLASS_PATH . 'CustomHooks.php';
include_once CLASS_PATH . 'FileRW.php';
include_once CLASS_PATH . "Roles.php";
include_once CLASS_PATH . 'jwt.php';
include_once CLASS_PATH . "Pages.php";
include_once CLASS_PATH . "Plugin/PluginMenuBar.class.php";
include_once CLASS_PATH . "Shortcodes.php";
include_once CLASS_PATH . "Statystic.php";
include_once CLASS_PATH . "admin/adminKokpitMenu.php";

include_once INCLUDES_PATH . "mp_functions.php";

include CLASS_PATH . "Plugin/PluginInfo.class.php";
include CLASS_PATH . "Plugin/PluginUpdater.class.php";
//v18


include_once CLASS_PATH . 'Phrases.php';
include_once ROOT . "wpShopPluginUpdate.php";
include_once ROOT . "wpShopPluginUuninstall.php";
#endregion

#region Ksiegowosc / accountancy

include_once ACCOUNTANCY . "Seller.php";
include_once ACCOUNTANCY . "Buyer.php";


include_once ACCOUNTANCY . "DataRefactor.php";
include_once ACCOUNTANCY . "BuildTable.php";
include_once ACCOUNTANCY . 'NumberToTextPL.php';
include_once ACCOUNTANCY . "DOC_numeration.php";
include_once ACCOUNTANCY . 'HtmlToPDF.php';


include_once ACCOUNTANCY . "addInvoice.php";
include_once ACCOUNTANCY . "addPZ.php";
include_once ACCOUNTANCY . "addWZ.php";


include_once ACCOUNTANCY . "DOC_WZ.php";
include_once ACCOUNTANCY . "DOC_PZ.php";
include_once ACCOUNTANCY . "DOC_FVS.php";


include_once ACCOUNTANCY . "PrefabPDF.php";
include_once ACCOUNTANCY . "HTML_FVS.php";
include_once ACCOUNTANCY . "HTML_WZ.php";
include_once ACCOUNTANCY . "HTML_PZ.php";

#endregion

