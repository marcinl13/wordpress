<?php

namespace http;

interface IHttpStatusCode{
  const Continue = 100; 
	const Switching_Protocols = 101; 
	const OK = 200; 
	const Created = 201; 
	const Accepted = 202; 
	const Non_Authoritative_Information = 203; 
	const No_Content = 204; 
	const Reset_Content = 205; 
	const Partial_Content = 206; 
	const Multiple_Choices = 300; 
	const Moved_Permanently = 301; 
	const Moved_Temporarily = 302; 
	const See_Other = 303; 
	const Not_Modified = 304; 
	const Use_Proxy = 305; 
	const Bad_Request = 400; 
	const Unauthorized = 401; 
	const Payment_Required = 402; 
	const Forbidden = 403; 
	const Not_Found = 404; 
	const Method_Not_Allowed = 405; 
	const Not_Acceptable = 406; 
	const Proxy_Authentication_Required = 407; 
	const Request_Time_out = 408; 
	const Conflict = 409; 
	const Gone = 410; 
	const Length_Required = 411; 
	const Precondition_Failed = 412; 
	const Request_Entity_Too_Large = 413; 
	const Request_URI_Too_Large = 414; 
	const Unsupported_Media_Type = 415; 
	const Internal_Server_Error = 500; 
	const Not_Implemented = 501; 
	const Bad_Gateway = 502; 
	const Service_Unavailable = 503; 
	const Gateway_Time_out = 504; 
	const HTTP_Version_not_supported = 505; 
}