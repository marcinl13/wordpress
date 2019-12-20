<?php

namespace TableDB;

interface ITableNames
{
  const Products = 'products';
  const Orders = 'orders';
  const Category = 'categories';
  const Status = 'zamowienia_statusy';
  const Transactions = 'transactions';
  const Vat = 'tax_rates';

  //v18
  const Documents18 = 'documents';
  const Invoices = 'invoices';
  const InvoicesDetails = 'invoices_details';
  const Magazine18 = 'magazine';  
  const Users = 'users';
}
